<?php


namespace App\Controller;


use App\Controller\FileTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BaseController extends AbstractController
{

    protected $annee;
}
