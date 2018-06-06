<?php
/**
 * Created by PhpStorm.
 * User: Ahmed
 * Date: 05-Jun-18
 * Time: 6:57 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/datatable")
 */
class DatatableController extends Controller
{
    /**
     * @Route("/users/list", name="datatable_ajax_users_list")
     * @Template()
     * @param Request $request
     * @return Response
     */
    public function usersListAction(Request $request)
    {

        $get = $request->query->all();
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
        * you want to insert a non-database field (for example a counter or static image)
        */
//        dump($get);
        $columnss = array('username', 'email', 'enabled', 'roles', 'lastLogin', 'id');
        $get['columnss'] = &$columnss;
        $em = $this->getDoctrine()->getManager();
        $rResult = $em->getRepository('AppBundle:User')->ajaxTable($get, true)->getArrayResult();
        /*
         * Output
         */
        $output = array(
            "draw" => intval($get['draw']),
            "recordsTotal" => $em->getRepository('AppBundle:User')->getCount(),
            "recordsFiltered" => $em->getRepository('AppBundle:User')->getFilteredCount($get),
            "data" => array(),
        );
        $btn = '<div class="btn-group">
                    <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-left" role="menu">
                        <li class="divider"> </li>
                        <li>
                            <a href="javascript:user_delete([X]);">
                                <i class="icon-trash"></i> Supprimer 
                            </a>
                        </li>
                        <li class="divider"> </li>
                        <li>
                            <a href="javascript:user_edit([X]);">
                                <i class="icon-pencil"></i> Modifier 
                            </a>
                        </li>
                        <li class="divider"> </li>
                        <li>
                            <a href="javascript:user_change_status([X]);">
                                <i class="icon-settings"></i> Changer Le Statut 
                            </a>
                        </li>
                        <li class="divider"> </li>
                    </ul>
                </div>';
        foreach ($rResult as $aRow) {
            $row = array();
            for ($i = 0; $i < count($columnss); $i++) {
                if ($columnss[$i] == "enabled") {
                    if($aRow[$columnss[$i]] ==true){
                        $row[]= '<span class="label label-sm label-success"> Activée </span>';
                    }else{
                        $row[]= '<span class="label label-sm label-danger"> Désactivée</span>';
                    }
                }elseif ($columnss[$i] == "id") {
                    $row[] = str_replace('[X]',$aRow[$columnss[$i]], $btn) ;
                }elseif ($columnss[$i] == "lastLogin" && $aRow[$columnss[$i]] instanceof  \DateTime){
                    $date = $aRow[$columnss[$i]];
                    $row[]=  $date->format('d-m-Y H:i:s');
                }elseif ($columnss[$i] == 'roles') {
                    $row[] = implode('<br>',$aRow[$columnss[$i]] ) ;
                } elseif ($columnss[$i] != ' ') {
                    $row[] = $aRow[$columnss[$i]];
                }
            }
            $output['data'][] = $row;
        }
        unset($rResult);

        return new Response(
            json_encode($output)
        );
    }

}