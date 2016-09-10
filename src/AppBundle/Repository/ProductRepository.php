<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ProductTag;
use AppBundle\Entity\Tag;
use AppBundle\Model\MediaTag;
use AppBundle\Model\TagMatch;
use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param MediaTag[] $mediaTags
     *
     * @return array
     */
    public function getProductsByMediaTags(array $mediaTags)
    {

        /** @var EntityRepository $tagRepository */
        $tagRepository = $this->getEntityManager()->getRepository('AppBundle\Entity\Tag');

        $tagNames = [];
        foreach($mediaTags as $mediaTag) {
            $tagNames[] = $mediaTag->getName();
        }

        /** @var Tag[] $tags */
        $tags = $tagRepository->findBy([
            'name' => $tagNames
        ]);

        $tagMatches  = [];
        foreach($tags as $tag) {
            foreach($mediaTags as $mediaTag) {
                if ($tag->getName() == $mediaTag->getName()) {
                    $tagMatches[] = new TagMatch($tag, $mediaTag);
                    continue 2;
                }
            }
        }


        $groupedByProduct = [];

        /** @var TagMatch $tagMatch */
        foreach($tagMatches as $tagMatch) {
            /** @var ProductTag $productTag */
            foreach($tagMatch->getTag()->getProductTags() as $productTag) {
                $productId = $productTag->getProduct()->getId();

                if (!isset($groupedByProduct[$productId])) {
                    $groupedByProduct[$productId] = ['total_score' => 0, 'matches' => []];
                }

                $groupedByProduct[$productId]['matches'][] = $tagMatch;
                $groupedByProduct[$productId]['product'] = $productTag->getProduct();
                $groupedByProduct[$productId]['total_score'] += $tagMatch->getScore() * $productTag->getScore();
            }
        }

        usort($groupedByProduct, function($a, $b) {
            $a_ = $a['total_score'] / (count($a['matches']) / 2);
            $b_ = $b['total_score'] / (count($b['matches']) / 2);

            if ($a_ < $b_) {
                return 1;
            }
            if ($a_ > $b_) {
                return -1;
            }

            return 0;
        });


        return $groupedByProduct;
    }
}
