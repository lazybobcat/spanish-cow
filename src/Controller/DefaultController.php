<?php
/**
 * This file is part of the spanish-cow project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: loicb
 * Date: 29/05/18
 * Time: 09:19
 */

namespace App\Controller;

use Nvision\SpanishCowAdapter\Client;
use Nvision\SpanishCowAdapter\SpanishCow;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Translation\Common\Model\Message;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request)
    {
        if (null === $this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->redirectToRoute('project_list');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(Client $client, SpanishCow $adapter)
    {
//        $client->login();

        $message = new Message('homepages', 'messages', 'fr');
        $adapter->create($message);

        return $this->render('default/homepage.html.twig');
    }
}
