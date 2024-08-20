<?php

namespace gamboamartin\empleado\models;

use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\notificaciones\models\not_adjunto;
use gamboamartin\notificaciones\models\not_emisor;
use gamboamartin\notificaciones\models\not_mensaje;
use gamboamartin\notificaciones\models\not_receptor;
use gamboamartin\notificaciones\models\not_rel_mensaje;
use PDO;
use stdClass;

class _email
{
    public const ERROR_CORREO_NO_VALIDO = "El correo '%s' no es válido.";
    public const ERROR_CORREO_NO_EXISTE = "El correo '%s' no es existe.";
    public const ERROR_FILTRO = "Error al filtrar al '%s'";
    public const ERROR_VALIDACION = "Error al validar el correo '%s'";
    public const ERROR_CORREO_NO_ENCONTRADO = "No se encontró el emisor con el correo '%s'";
    public const ERROR_AL_INSERTAR = "Error al insertar al '%s'";
    private PDO $link;

    public function __construct(PDO $link)
    {
        $this->link = $link;
    }

    public function correo_validacion(string $correo, modelo $modelo, string $campo): array|stdClass
    {
        $validacion = $this->validar_correo($correo);
        if (!$validacion) {
            $mensaje_error = sprintf(self::ERROR_CORREO_NO_VALIDO, $correo);
            return (new errores())->error(mensaje: $mensaje_error, data: $correo);
        }

        $filtro = array();
        $filtro['email'] = $correo;
        $datos = $modelo->filtro_and(filtro: $filtro);
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_FILTRO, $campo);
            return (new errores())->error(mensaje: $mensaje_error, data: $datos);
        }

        return $datos;
    }

    public function emisor(string $correo): array
    {
        $datos = $this->correo_validacion(correo: $correo, modelo: (new not_emisor(link: $this->link)), campo: 'emisor');
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_VALIDACION, $correo);
            return (new errores())->error(mensaje: $mensaje_error, data: $datos);
        }

        if ($datos->n_registros == 0) {
            $mensaje_error = sprintf(self::ERROR_CORREO_NO_EXISTE, $correo);
            return (new errores())->error(mensaje: $mensaje_error, data: $datos);
        }

        return $datos->registros[0];
    }

    public function receptor(string $correo): array
    {
        $datos = $this->correo_validacion(correo: $correo, modelo: (new not_receptor(link: $this->link)), campo: 'receptor');
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_VALIDACION, $correo);
            return (new errores())->error(mensaje: $mensaje_error, data: $datos);
        }

        if ($datos->n_registros == 0) {
            $alta_not_receptor = (new not_receptor(link: $this->link))->alta_registro(
                array(
                    'email' => $correo,
                    'descripcion' => $correo,
                    'codigo' => $correo,
                ));
            if (errores::$error) {
                $mensaje_error = sprintf(self::ERROR_AL_INSERTAR, 'receptor');
                return (new errores())->error(mensaje: $mensaje_error, data: $alta_not_receptor);
            }

            return (new not_receptor(link: $this->link))->registro(registro_id: $alta_not_receptor->registro_id);
        }

        return $datos->registros[0];
    }

    public function mensaje(string $asunto, string $mensaje, int $emisor): array
    {
        $UUID = (new not_mensaje(link: $this->link))->get_codigo_aleatorio(10);
        $alta_not_mensaje = (new not_mensaje(link: $this->link))->alta_registro(
            array(
                'asunto' => $asunto,
                'mensaje' => $mensaje,
                'not_emisor_id' => $emisor,
                'descripcion' => $asunto . $UUID,
                'codigo' => $UUID,
            ));
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_AL_INSERTAR, 'mensaje');
            return (new errores())->error(mensaje: $mensaje_error, data: $alta_not_mensaje);
        }

        return (new not_mensaje(link: $this->link))->registro(registro_id: $alta_not_mensaje->registro_id);
    }

    public function mensaje_receptor(int $mensaje, int $receptor): array
    {
        $alta_not_mensaje_receptor = (new not_rel_mensaje(link: $this->link))->alta_registro(
            array(
                'not_mensaje_id' => $mensaje,
                'not_receptor_id' => $receptor,
            ));
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_AL_INSERTAR, 'relación mensaje receptor');
            return (new errores())->error(mensaje: $mensaje_error, data: $alta_not_mensaje_receptor);
        }

        return (new not_rel_mensaje(link: $this->link))->registro(registro_id: $alta_not_mensaje_receptor->registro_id);
    }

    public function adjunto(int $mensaje, int $documento): array
    {
        $alta_not_adjunto = (new not_adjunto(link: $this->link))->alta_registro(
            array(
                'not_mensaje_id' => $mensaje,
                'doc_documento_id' => $documento,
            ));
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_AL_INSERTAR, 'adjunto');
            return (new errores())->error(mensaje: $mensaje_error, data: $alta_not_adjunto);
        }

        return (new not_rel_mensaje(link: $this->link))->registro(registro_id: $alta_not_adjunto->registro_id);
    }

    public function adjuntos(int $mensaje, array $documentos): array
    {
        $resultado = array();
        foreach ($documentos as $documento) {
            $resultado[] = $this->adjunto(mensaje: $mensaje, documento: $documento);
        }

        return $resultado;
    }




    public function validar_correo($correo): mixed
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL);
    }

}