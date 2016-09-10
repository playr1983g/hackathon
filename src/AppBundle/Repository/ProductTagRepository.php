<?php

namespace AppBundle\Repository;

/**
 * ProductTagsProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductTagRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param MediaTag[] $mediaTags
     *
     * @return array
     */
    public function getProductsByMediaTags(array $mediaTags)
    {
        return $this->findBy([], [], 10);
    }
}
