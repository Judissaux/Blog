<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @extends ServiceEntityRepository<Menu>
 *
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function save(Menu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Menu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * @return  Menu []
     * 
     */
    public function findAllForTwig(): array
    {   // Requete sql qui permet de récupérer les menu qui sont coché comme visible
        return $this->createQueryBuilder('m')
            ->where('m.isVisible = true')
            ->orderBy('m.menuOrder')
            ->getQuery()
            ->getResult();
        
    }

    public function getIndexQueryBuilder(string $field):QueryBuilder
    {
        return $this->createQueryBuilder('m')
                ->where ("m.$field IS NOT NULL OR (m.page IS NULL AND m.article IS NULL AND m.link IS NULL AND m.category IS NULL)");
        
    }
}
