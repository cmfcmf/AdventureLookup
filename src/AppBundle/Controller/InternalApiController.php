<?php

namespace AppBundle\Controller;

use AppBundle\Exception\FieldDoesNotExistException;
use AppBundle\Field\FieldProvider;
use AppBundle\Service\AdventureSearch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InternalApiController extends Controller
{
    /**
     * @Route("/autocomplete/field/{fieldName}", name="api_autocomplete_field")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function autocompleteFieldValueAction(Request $request, FieldProvider $fieldProvider, AdventureSearch $adventureSearch, string $fieldName)
    {
        try {
            $field = $fieldProvider->getField($fieldName);
        } catch (FieldDoesNotExistException $e) {
            throw new NotFoundHttpException();
        }

        $q = $request->query->get('q');
        $results = $adventureSearch->autocompleteFieldContent($field, $q);

        return new JsonResponse($results);
    }

    /**
     * @Route("/autocomplete/similar-titles", name="similar_titles_search")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function findSimilarTitlesAction(Request $request, AdventureSearch $adventureSearch)
    {
        $q = $request->query->get('q', false);
        if (false === $q) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($adventureSearch->similarTitles($q));
    }
}
