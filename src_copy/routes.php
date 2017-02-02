<?php
/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="iir.netip.hu",
 *     basePath="/v1",
 *     @SWG\Info(
 *         version="1.0",
 *         title="IIR REST API",
 *         description="IIR DB REST INTERFACE",
 *         @SWG\Contact(name="MM Team", email="irf-multimedia@niif.hu")
 *     ),
 *   security={{
 *     "Bearer":{
 *          "type": "apiKey",
 *          "name": "Authorization",
 *          "in": "header"
 *          }
 *   }},
 * )
 */
/**
 * @SWG\SecurityScheme(
 *   securityDefinition="Bearer",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization",
 * )
 */

use App\Action\OtherAction;
use App\Action\IntezmenyAction;
use App\Action\CimAction;
use App\Action\EmberAction;
use App\Action\EszkozAction;
use App\Action\DropsAction;
use Swagger\Swagger;

// Redirect API Docs (The method is fake .htaccess RewriteRule)
$app->get('/', function ($request, $response, $args) {
    return $response->withRedirect($this->router->pathFor('docs'));
});

// Swagger UI
$app->get('/v1/docs', function ($request, $response, $args) {
///    $response->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

    return $this->view->render($response->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0'), 'swagger/index.html', [
        'jsonUrl' => $this->router->pathFor('swagger'),
        ///'jsonUrl' => $request->getUri()->getBasePath().'/swagger.json', // static file (generate cli)
        'docsUrl' => $this->router->pathFor('docs'),
        'templatesUrl' => $request->getUri()->getBasePath().'/templates/swagger'
    ]);
})->setName('docs');

// Swagger Json
$app->get('/v1/swagger', function ($request, $response) {
  $swagger = \Swagger\scan('../app');
  return $response->withHeader('Content-Type', 'application/json')
                  ->write($swagger);
})->setName('swagger');

// Intezmeny
$app->group('/v1/intezmeny', function () use ($container) {
    $this->get   ('',                                  IntezmenyAction::class.':getAllIntezmeny');
    $this->post  ('',                                  IntezmenyAction::class.':addIntezmeny');
    $this->get   ('/{intezmeny_id:[0-9]+}',            IntezmenyAction::class.':getIntezmeny');
    $this->put   ('/{intezmeny_id:[0-9]+}',            IntezmenyAction::class.':updateIntezmeny');
    $this->delete('/{intezmeny_id:[0-9]+}',            IntezmenyAction::class.':deleteIntezmeny');

    /// route alias /{intezmeny_id:[0-9]+}
    $this->get   ('/vpid/{vpid:[0-9]+}',               IntezmenyAction::class.':getIntezmeny');

    $this->get   ('/{intezmeny_id:[0-9]+}/kapcsolat',                                       IntezmenyAction::class.':getIntezmenyKapcsolat');
    $this->post  ('/{intezmeny_id:[0-9]+}/kapcsolat',                                       IntezmenyAction::class.':addIntezmenyKapcsolat');
    $this->get   ('/{intezmeny_id:[0-9]+}/kapcsolat/{intezmeny_kapcsolat_id:[0-9]+}',       IntezmenyAction::class.':getIntezmenyKapcsolatId');
    $this->put   ('/{intezmeny_id:[0-9]+}/kapcsolat/{intezmeny_kapcsolat_id:[0-9]+}',       IntezmenyAction::class.':updateIntezmenyKapcsolat');
    $this->delete('/{intezmeny_id:[0-9]+}/kapcsolat/{intezmeny_kapcsolat_id:[0-9]+}',       IntezmenyAction::class.':deleteIntezmenyKapcsolat');

    $this->get   ('/{intezmeny_id:[0-9]+}/iktato',                                          IntezmenyAction::class.':getIntezmenyIktato');
    $this->post  ('/{intezmeny_id:[0-9]+}/iktato',                                          IntezmenyAction::class.':addIntezmenyIktato');
    $this->get   ('/{intezmeny_id:[0-9]+}/iktato/{intezmeny_iktato_id:[0-9]+}',             IntezmenyAction::class.':getIntezmenyIktatoId');
    $this->put   ('/{intezmeny_id:[0-9]+}/iktato/{intezmeny_iktato_id:[0-9]+}',             IntezmenyAction::class.':updateIntezmenyIktato');
    $this->delete('/{intezmeny_id:[0-9]+}/iktato/{intezmeny_iktato_id:[0-9]+}',             IntezmenyAction::class.':deleteIntezmenyIktato');    

    $this->get   ('/{intezmeny_id:[0-9]+}/fenntarto',                                       IntezmenyAction::class.':getIntezmenyFenntarto');
    $this->post  ('/{intezmeny_id:[0-9]+}/fenntarto',                                       IntezmenyAction::class.':addIntezmenyFenntarto');
    $this->get   ('/{intezmeny_id:[0-9]+}/fenntarto/{intezmeny_fenntarto_id:[0-9]+}',       IntezmenyAction::class.':getIntezmenyFenntartoId');
    $this->put   ('/{intezmeny_id:[0-9]+}/fenntarto/{intezmeny_fenntarto_id:[0-9]+}',       IntezmenyAction::class.':updateIntezmenyFenntarto');
    $this->delete('/{intezmeny_id:[0-9]+}/fenntarto/{intezmeny_fenntarto_id:[0-9]+}',       IntezmenyAction::class.':deleteIntezmenyFenntarto'); 

    $this->get   ('/{intezmeny_id:[0-9]+}/mysql',                                           IntezmenyAction::class.':getIntezmenyMySQL');
    $this->post  ('/{intezmeny_id:[0-9]+}/mysql',                                           IntezmenyAction::class.':addIntezmenyMySQL');
    $this->get   ('/{intezmeny_id:[0-9]+}/mysql/{intezmeny_mysql_szolgaltatas_id:[0-9]+}',  IntezmenyAction::class.':getIntezmenyMySQLId');
    $this->put   ('/{intezmeny_id:[0-9]+}/mysql/{intezmeny_mysql_szolgaltatas_id:[0-9]+}',  IntezmenyAction::class.':updateIntezmenyMySQL');
    $this->delete('/{intezmeny_id:[0-9]+}/mysql/{intezmeny_mysql_szolgaltatas_id:[0-9]+}',  IntezmenyAction::class.':deleteIntezmenyMySQL'); 

    $this->get   ('/{intezmeny_id:[0-9]+}/ember',      IntezmenyAction::class.':getIntezmenyEmber');
    $this->get   ('/{intezmeny_id:[0-9]+}/eszkoz',     IntezmenyAction::class.':getIntezmenyEszkoz');

    /// auto generate with LDAP
    $this->get   ('/{intezmeny_id:[0-9]+}/domain',     IntezmenyAction::class.':getIntezmenyDomain');
});

// Cim
$app->group('/v1/cim', function () use ($container) {
    $this->get   ('',                               CimAction::class.':getAllCim');
    $this->get   ('/{cim_id:[0-9]+}',               CimAction::class.':getCim');
    $this->post  ('',                               CimAction::class.':addCim');
    $this->put   ('/{cim_id:[0-9]+}',               CimAction::class.':updateCim');
    $this->delete('/{cim_id:[0-9]+}',               CimAction::class.':deleteCim');
});

// Eszkoz
$app->group('/v1/eszkoz', function () use ($container) {
    $this->get   ('',                               EszkozAction::class.':getAllEszkoz');
    $this->get   ('/{eszkoz_id:[0-9]+}',            EszkozAction::class.':getEszkoz');
    $this->post  ('',                               EszkozAction::class.':addEszkoz');
    $this->put   ('/{eszkoz_id:[0-9]+}',            EszkozAction::class.':updateEszkoz');
    $this->delete('/{eszkoz_id:[0-9]+}',            EszkozAction::class.':deleteEszkoz');
    $this->get   ('/{eszkoz_id:[0-9]+}/specialis',  EszkozAction::class.':getEszkozSpecialis');
    $this->put   ('/{eszkoz_id:[0-9]+}/specialis',  EszkozAction::class.':updateEszkozSpecialis');
});

// Ember
$app->group('/v1/ember', function () use ($container) {
    $this->get   ('',                               EmberAction::class.':getAllEmber');
    $this->get   ('/{ember_id:[0-9]+}',             EmberAction::class.':getEmber');
    $this->post  ('',                               EmberAction::class.':addEmber');
    $this->put   ('/{ember_id:[0-9]+}',             EmberAction::class.':updateEmber');
    $this->delete('/{ember_id:[0-9]+}',             EmberAction::class.':deleteEmber');
});

// Drops
$app->group('/v1/drops', function () use ($container) {
    $this->get   ('/{eszkoz_id:[0-9]+}/socket',     DropsAction::class.':getDropsSocket');
    $this->get   ('/{eszkoz_id:[0-9]+}/static',     DropsAction::class.':getDropsStatic');
    $this->get   ('/{eszkoz_id:[0-9]+}/gen',        DropsAction::class.':getDropsGen');
});

/// Socket Address
$app->group('/v1/socket/address', function () use ($container) {
    $this->get   ('',                               DropsAction::class.':getAllSocketAddress');
    $this->get   ('/{socket_address_id:[0-9]+}',    DropsAction::class.':getSocketAddress');
    $this->get   ('/eszkoz/{eszkoz_id:[0-9]+}',     DropsAction::class.':getSocketAddressEszkoz');
    $this->post  ('',                               DropsAction::class.':addSocketAddress');
    $this->put   ('/{socket_address_id:[0-9]+}',    DropsAction::class.':updateSocketAddress');
    $this->delete('/{socket_address_id:[0-9]+}',    DropsAction::class.':deleteSocketAddress');
    $this->delete('/eszkoz/{eszkoz_id:[0-9]+}',     DropsAction::class.':deleteSocketAddressEszkoz');
});

/// Socket Vlan
$app->group('/v1/socket/vlan', function () use ($container) {
    $this->get   ('',                               DropsAction::class.':getAllSocketVlan');
    $this->get   ('/{socket_vlan_id:[0-9]+}',       DropsAction::class.':getSocketVlan');
    $this->get   ('/eszkoz/{eszkoz_id:[0-9]+}',     DropsAction::class.':getSocketVlanEszkoz');
    $this->post  ('',                               DropsAction::class.':addSocketVlan');
    $this->put   ('/{socket_vlan_id:[0-9]+}',       DropsAction::class.':updateSocketVlan');
    $this->delete('/{socket_vlan_id:[0-9]+}',       DropsAction::class.':deleteSocketVlan');
    $this->delete('/eszkoz/{eszkoz_id:[0-9]+}',     DropsAction::class.':deleteSocketVlanEszkoz');
});

// Other
/// Fenntarto
$app->group('/v1/fenntarto', function () use ($container) {
    $this->get   ('',                               OtherAction::class.':getAllFenntarto');
    $this->get   ('/{fenntarto_id:[0-9]+}',         OtherAction::class.':getFenntarto');
    $this->post  ('',                               OtherAction::class.':addFenntarto');
    $this->put   ('/{fenntarto_id:[0-9]+}',         OtherAction::class.':updateFenntarto');
    $this->delete('/{fenntarto_id:[0-9]+}',         OtherAction::class.':deleteFenntarto');
});

/// Garancia
$app->group('/v1/garancia', function () use ($container) {
    $this->get   ('',                               OtherAction::class.':getAllGarancia');
    $this->get   ('/{garancia_id:[0-9]+}',          OtherAction::class.':getGarancia');
    $this->post  ('',                               OtherAction::class.':addGarancia');
    $this->put   ('/{garancia_id:[0-9]+}',          OtherAction::class.':updateGarancia');
    $this->delete('/{garancia_id:[0-9]+}',          OtherAction::class.':deleteGarancia');
});

/// Support
$app->group('/v1/support', function () use ($container) {
    $this->get   ('',                               OtherAction::class.':getAllSupport');
    $this->get   ('/{support_id:[0-9]+}',           OtherAction::class.':getSupport');
    $this->post  ('',                               OtherAction::class.':addSupport');
    $this->put   ('/{support_id:[0-9]+}',           OtherAction::class.':updateSupport');
    $this->delete('/{support_id:[0-9]+}',           OtherAction::class.':deleteSupport');
});

/// Megjegyzes
$app->group('/v1/megjegyzes', function () use ($container) {
    $this->get   ('',                               OtherAction::class.':getAllMegjegyzes');
    $this->get   ('/{megjegyzes_id:[0-9]+}',        OtherAction::class.':getMegjegyzes');
    $this->post  ('',                               OtherAction::class.':addMegjegyzes');
    $this->put   ('/{megjegyzes_id:[0-9]+}',        OtherAction::class.':updateMegjegyzes');
    $this->delete('/{megjegyzes_id:[0-9]+}',        OtherAction::class.':deleteMegjegyzes');
});

/// Keresés
$app->group('/v1/search', function () use ($container) {
    $this->get   ('/{term}',                        OtherAction::class.':getSearch');
});

/// Enum
$app->group('/v1/enum', function () use ($container) {
    $this->get   ('',                               OtherAction::class.':getAllEnum');
});