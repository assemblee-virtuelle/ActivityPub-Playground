<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CasController extends AbstractController
{
    /**
     * @Route("/cas/login", name="cas_login")
     */
    public function loginAction(Request $request)
    {
        \phpCAS::setDebug(false);
        \phpCAS::client(CAS_VERSION_2_0, 'monprofil.colibris-lemouvement.org', 443, '/cas', true);
        \phpCAS::setNoCasServerValidation();
        \phpCAS::setLang(PHPCAS_LANG_FRENCH);
        \phpCAS::forceAuthentication();
        if (\phpCAS::isAuthenticated()) {
            $userAttributes = \phpCAS::getAttributes();
//            var_dump($userAttributes);
            $redirectUrl = $request->query->get('redirectUrl');
            if( !$redirectUrl ) throw new \Exception('No redirectUrl found');
            return $this->redirect($redirectUrl . '?token=MYTOKEN');

//          'uid' => string '60534' (length=5)
//          'uuid' => string '08b07984-bbbd-47fd-8fc6-d1582f1c0d56' (length=36)
//          'langcode' => string 'fr' (length=2)
//          'preferred_langcode' => string 'fr' (length=2)
//          'preferred_admin_langcode' => string 'fr' (length=2)
//          'name' => string 'srosset81' (length=9)
//          'pass' => string '$S$E8PGjB.OCYVAP0TSnoY/hoxjq/.8Y24rQ6H8qJ5CFjdt5MSEg9jl' (length=55)
//          'mail' => string 'srosset81@gmail.com' (length=19)
//          'timezone' => string 'Europe/Berlin' (length=13)
//          'status' => string '1' (length=1)
//          'created' => string '1537521204' (length=10)
//          'changed' => string '1555532612' (length=10)
//          'access' => string '1555843095' (length=10)
//          'login' => string '1555532579' (length=10)
//          'init' => string 'srosset81@gmail.com' (length=19)
//          'roles' => string '' (length=0)
//          'default_langcode' => string '1' (length=1)
//          'path' => string '' (length=0)
//          'field_address' => string '{"langcode":null,"country_code":"FR","administrative_area":"","locality":"Chantilly","dependent_locality":"","postal_code":"60500","sorting_code":"","address_line1":"10 ter impasse Souchier","address_line2":"","organization":null,"given_name":null,"additional_name":null,"family_name":null}' (length=290)
//          'field_avatar' => string '{"target_id":"5","alt":"","title":"","width":"117","height":"128"}' (length=66)
//          'field_consent_gdpr' => string '{"target_id":"1","target_revision_id":"1","agreed":"1","user_id":"60534","date":"2019-04-17 22:23:32","user_id_accepted":"60534","notes":""}' (length=140)
//          'field_first_name' => string 'Sébastien' (length=10)
//          'field_last_name' => string 'Rosset' (length=6)
//          'field_lat_lon' => string '{"value":"POINT(2.468857 49.195031)","geo_type":"Point","lat":49.195031,"lon":2.468857,"left":2.468857,"top":49.195031,"right":2.468857,"bottom":49.195031,"geohash":"u09zb5tvbd3n","latlon":"49.195031,2.468857"}' (length=210)
//          'field_learning360_id' => string '5ba4b635c8837d17198348cb' (length=24)
//          'field_newsletter_colibris' => string '0' (length=1)
        }
    }
}