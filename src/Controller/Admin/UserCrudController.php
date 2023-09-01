<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class UserCrudController extends AbstractAppCrudController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\IdField::new('id')->hideOnForm(),
            Field\EmailField::new('email'),
            Field\ArrayField::new('roles'),
            Field\BooleanField::new('emailVerified'),
            Field\BooleanField::new('locked'),
            Field\DateField::new('registrationDate')->hideOnForm(),
            Field\DateField::new('lastLogin')->hideOnForm(),
//            Field\AssociationField::new('pros')->autocomplete(),
//            Field\AssociationField::new('clients')->autocomplete(),
        ];
    }


    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('email')
            ->add('roles')
            ->add('emailVerified')
            ->add('locked')
            ->add('registrationDate')
            ->add('lastLogin')
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateFormat('Y-MM-dd')
            ->setPageTitle('index', $this->getIndexTitle())
            // ...
            ;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder
    {

        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        switch ($this->getActiveDrain()) {
            case 'email_not_verified':
                $qb->where('entity.emailVerified = false');
                break;
            case 'locked':
                $qb->where('entity.locked = true');
                break;
        }
        return $qb;
    }


    public function configureActions(Actions $actions): Actions
    {

        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, $this->getDrainAction('email_not_verified'))
            ->add(Crud::PAGE_INDEX, $this->getDrainAction('locked'))
//           ->add(Crud::PAGE_INDEX, $this->getFocusAction(ClientCrudController::class, 'See clients', DataManagerController::ICON_CLIENT))
//            ->add(Crud::PAGE_INDEX, $this->getFocusAction(ProCrudController::class, 'See pros', DataManagerController::ICON_PRO))
            // this will forbid to create or delete entities in the backend
            ->disable(Action::NEW, Action::DELETE)
            ;
    }

}
