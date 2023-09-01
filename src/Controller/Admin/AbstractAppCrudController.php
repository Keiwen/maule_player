<?php

namespace App\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractAppCrudController extends AbstractCrudController
{

    /*
     * INTRODUCING CONCEPT: DRAIN
     * Drain is a global action (info button) that apply a given query on index page listing.
     * On CRUDController::configureActions(), add action
     * >add(Crud::PAGE_INDEX, $this->getDrainAction('drainId', 'drainLabel'))
     * On CRUDController::configureCrud(), you can get auto-generated title
     * ->setPageTitle('index', $this->getIndexTitle())
     * If some drain is active, it will display 'only {drainId}'
     * Now on CRUDController::createIndexQueryBuilder(), you can get the querybuilder from parent and alter it.
     * $queryBuilder->where('entity.{someField} = {smthg}');
     * You'll find drain id by using this method
     * $drainId = $this->getActiveDrain()
     * Note: drain action is generated as URL instead of method of current controller in order to be used from outside.
     */

    /*
     * INTRODUCING CONCEPT: FOCUS
     * Focus is a action that apply a given query on index page listing.
     * It is designed for entities association, from Many ('container') to One ('contained').
     * Action apply to containing entity and redirect to contained list of entity
     * On containerCRUDController::configureActions(), add action
     * ->add(Crud::PAGE_INDEX, $this->getFocusAction({ContainedCRUDController}::class, 'focusLabel', 'focusIcon))
     * On ContainedCRUDController::configureCrud(), you can get auto-generated title
     * ->setPageTitle('index', $this->getIndexTitle())
     * If some focus is active, it will display 'focus {containerEntity->__toString()}'
     * Query builder in method createIndexQueryBuilder is described in this class
     * $queryBuilder->where('entity.' . $containerEntity . ' = :id');
     * If needed, you'll find focus data by using these methods
     * $focusEntity = $this->getActiveFocusEntity()
     * $focusEntityId = $this->getActiveFocusId()
     * $focusLabel = $this->getActiveFocusLabel()
     */


    protected $adminUrlGenerator;
    protected $focusTargetControllers = [];
    protected $focusEntitiesGenerated = [];
    protected $request;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, RequestStack $requestStack)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getIndexTitle(): string
    {
        $indexTitle = '%entity_label_plural%';
        if (!empty($this->getActiveFocusEntity())) {
            $focusLabel = $this->getActiveFocusLabel() ?? $this->getActiveFocusEntity() . ' #' . $this->getActiveFocusId();
            $indexTitle .= ' (focus ' . $focusLabel . ')';
        }
        if (!empty($this->getActiveDrain())) {
            $indexTitle .= ' (only ' . str_replace('_', ' ', $this->getActiveDrain()) . ')';
        }
        return $indexTitle;
    }


    public function getDrainAction(string $drainId, ?string $label = null): Action
    {
        $adminUrlGenerator = clone $this->adminUrlGenerator;
        $url = $adminUrlGenerator
            ->setController(static::class)
            ->setAction('index')
            ->set('drain', $drainId)
            ->generateUrl()
        ;
        return Action::new('drain_' . $drainId, $label ?? ucfirst(str_replace('_', ' ', $drainId)))
            ->createAsGlobalAction()
            ->setCssClass('btn btn-info')
            ->linkToUrl($url);
    }

    public function getFocusAction(string $targetCrudController, ?string $label, ?string $icon = null): Action
    {

        $focusEntityName = explode('\\', static::getEntityFqcn());
        $focusEntityName = end($focusEntityName);
        $targetControllerName = explode('\\', $targetCrudController);
        $targetControllerName = end($targetControllerName);
        /*
         * When running, application will create all actions first, then execute the callable given.
         * If several focus action are set, we need to store all target controller as we cannot access them directly in callable
         * That's why we will use an array as class attributes.
         * Note: use unshift instead of push, as first action handled in easy admin is the last defined in crud controller
         * see array's use in callable comment
         */
        array_unshift($this->focusTargetControllers, $targetCrudController);
        return Action::new('focus_' . $focusEntityName . '_' . $targetControllerName, $label, $icon)
            ->linkToUrl([$this, 'getFocusUrl']);
    }

    public function getFocusUrl($entity): string
    {
        if(!method_exists($entity, 'getId')) return '';

        /*
         * When several focus action are required, we will need to get the target controller.
         * We use here another array to count how many times this specific entity is using a focus action
         * Each time, we will get the following controller in list previously set
         */
        if(!isset($this->focusEntitiesGenerated[$entity->__toString()])) {
            //set entity count to 0 if not already set
            $this->focusEntitiesGenerated[$entity->__toString()] = 0;
        }
        $targetControllerIndex = $this->focusEntitiesGenerated[$entity->__toString()];
        //get controller with index = entity count
        $targetController = $this->focusTargetControllers[$targetControllerIndex];
        //increment count for next use
        $this->focusEntitiesGenerated[$entity->__toString()]++;

        $focusEntityName = explode('\\', static::getEntityFqcn());
        $focusEntityName = lcfirst(end($focusEntityName));
        $adminUrlFocusGenerator = clone $this->adminUrlGenerator;
        return $adminUrlFocusGenerator
            ->setController($targetController)
            ->setAction('index')
            ->set('focusEntity', $focusEntityName)
            ->set('focusId', $entity->getId())
            ->set('focusLabel', $entity->__toString())
            ->set('menuIndex', null)
            ->generateUrl()
        ;
    }


    public function getActiveDrain(): string
    {
        return $this->request->get('drain', '');
    }

    public function getActiveFocusEntity(): string
    {
        return $this->request->get('focusEntity', '');
    }

    public function getActiveFocusId(): string
    {
        return $this->request->get('focusId', '');
    }

    public function getActiveFocusLabel(): ?string
    {
        return $this->request->get('focusLabel');
    }


    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if (!empty($this->getActiveFocusEntity())) {
            $stringSanitizer = new StringSanitizer();
            $targetEntity = $stringSanitizer->get($this->getActiveFocusEntity(), StringSanitizer::FILTER_SLUG);
            $qb->where('entity.' . $targetEntity . ' = :id');
            $qb->setParameter('id', $stringSanitizer->get($this->getActiveFocusId(), StringSanitizer::FILTER_INT));
        }

        return $qb;
    }

}
