<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Movie;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;

class MovieController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Rest\View()
     *
     * @return Movie[]
     */
    public function getMoviesAction()
    {
        $movies = $this->getDoctrine()->getRepository(Movie::class)->findAll();

        return $movies;
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     *
     * @param Movie $movie
     *
     * @return Movie
     */
    public function postMoviesAction(Movie $movie)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        return $movie;
    }

    /**
     * @Rest\View()
     *
     * @param Movie|null $movie
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteMovieAction(Movie $movie = null)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();
    }

    /**
     * @Rest\View()
     *
     * @param Movie|null $movie
     *
     * @return Movie|\FOS\RestBundle\View\View
     */
    public function getMovieAction(Movie $movie = null)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        return $movie;
    }
}