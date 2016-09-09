<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    public function listAction()
    {
        $productRepository = $this->get('doctrine')->getRepository('AppBundle:Product');
        $serializer = $this->get('jms_serializer');

        return new Response($serializer->serialize($productRepository->findAll(), 'json'));
    }

    public function uploadAction(Request $request)
    {
        $imageForm = $this->createForm(ImageType::class);
        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted()) {
            $tags = $this->get('clarifai.client')->getTagsByImageFile($imageForm->get('file')->getData());
            $tags = [];

            $productRepository = $this->get('doctrine')->getRepository('AppBundle:Product');
            $products = $productRepository->getProductsByTags($tags);

            $serializer = $this->get('jms_serializer');

            return new Response($serializer->serialize($products, 'json'));
        }

        return $this->render('AppBundle:Product:upload.html.twig', [
            'imageForm' => $imageForm->createView(),
        ]);
    }
}
