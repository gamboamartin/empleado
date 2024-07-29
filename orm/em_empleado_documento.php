<?php
namespace gamboamartin\empleado\models;
use base\orm\_modelo_parent;
use base\orm\_modelo_parent_sin_codigo;
use gamboamartin\documento\controllers\controlador_doc_documento;
use gamboamartin\documento\models\doc_documento;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class em_empleado_documento extends _modelo_parent_sin_codigo {
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'em_empleado_documento';
        $columnas = array($tabla=>false,  'em_empleado'=>$tabla, 'doc_documento' => $tabla,
            'doc_tipo_documento' => 'doc_documento', 'doc_extension' => 'doc_documento');
        $campos_obligatorios = array('doc_documento_id','em_empleado_id');

        $columnas_extra = array();

        $atributos_criticos =  array('doc_documento_id','em_empleado_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Documento Empleado';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $inserta_documento = $this->registra_documento();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error en insertar documento', data: $inserta_documento);
        }

        $validaciones = $this->validaciones($this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error en validaciones', data: $validaciones);
        }

        $this->registro = $this->inicializa_campos($this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar com_rel_agente', data: $r_alta_bd );
        }

        return $r_alta_bd;
    }

    public function registra_documento() : array|stdClass {
        if (!array_key_exists('documento', $_FILES)) {
            return array();
        }

        if (array_key_exists('em_empleado_id', $_POST)) {
            unset($_POST['em_empleado_id']);
            unset($this->registro['doc_tipo_documento_id']);
        }

        $alta_documento = (new controlador_doc_documento(link: $this->link))->alta_bd(header: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar documento', data: $alta_documento );
        }

        $this->registro['doc_documento_id'] = $alta_documento->registro_id;

        return $alta_documento;
    }

    final public function documentos(int $com_cliente, array $tipos_documentos)
    {
        $in = array();

        if (count($tipos_documentos) > 0) {
            $in['llave'] = 'doc_documento.doc_tipo_documento_id';
            $in['values'] = $tipos_documentos;
        }

        $documentos = $this->filtro_and(filtro: array('em_empleado.id' => $com_cliente), in: $in);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener documentos', data: $documentos);
        }

        return $documentos->registros;
    }

    protected function validaciones(array $registros): array
    {
        $documento = (new doc_documento(link: $this->link))->registro(registro_id: $registros['doc_documento_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener documento', data: $documento);
        }

        $filtro['doc_tipo_documento_id'] = $documento['doc_tipo_documento_id'];
        $filtro['em_empleado_id'] = $registros['em_empleado_id'];
        $existe = (new com_conf_tipo_doc_cliente(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar si existe', data: $filtro);
        }

        if(!$existe){
            return $this->error->error(mensaje: "No existe una configuración para el empleado y el tipo de documento",
                data: $filtro);
        }

        return $registros;
    }

    protected function inicializa_campos(array $registros): array
    {
        $registros['codigo'] = $this->get_codigo_aleatorio();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error generar codigo', data: $registros);
        }

        if(!isset($registros['descripcion'])){
            $descripcion = trim($registros['doc_documento_id']);
            $descripcion .= '-'.trim($registros['com_cliente_id']);
            $descripcion .= '-'.trim($registros['codigo']);
            $registros['descripcion'] = $descripcion;
        }

        return $registros;
    }

    public function elimina_bd(int $id): array|stdClass
    {
        if($id <= 0){
            return  $this->error->error(mensaje: 'El id no puede ser menor a 0 en '.$this->tabla, data: $id);
        }

        $cliente_documento = $this->registro(registro_id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro', data: $cliente_documento);
        }

        $r_elimina = parent::elimina_bd(id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar ',data:  $r_elimina);
        }

        $filtro['doc_documento.id'] = $cliente_documento['doc_documento_id'];
        $del = (new doc_documento(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_comprador_etapa',
                data:  $del);
        }

        return $r_elimina;
    }

}