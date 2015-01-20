<?php
/**
 * @license     LICENSE.txt
 * @author      MARKITOS
 * @package     Soporte
 * @filesource  Soporte/clases/Soporte.php
 * @desc        Clase utilitaria de soporte al desarrollo
 *
 */
class Soporte
{
    const SEPARADOR_VALORES_ARCHIVO = ":::";
    const SEPARADOR_CAMPOS_ARCHIVO  = "___";
	
    public static function generaArchivos ($cadena)
    {
        $retorno = array();

        $cadenaes = explode(self::SEPARADOR_VALORES_ARCHIVO, $cadena);

        foreach ($cadenaes as $unValor)
        {
            if (Soporte::cadenaVacia($unValor))
            {
                continue;
            }

            list ($etiqueta, $ruta) = explode (self::SEPARADOR_CAMPOS_ARCHIVO, $unValor);

            $archivo = new Archivo($ruta);
            $archivo->setEtiqueta($etiqueta);

            array_push($retorno, $archivo);
        }

        return $retorno;
    }
    
    public static function generaCadenaDesdeArchivos ($archivos = array())
    {
        $retorno = "";

        $separador = "";
        
        if (is_array($archivos) && count ($archivos) > 0)
        {
            foreach ($archivos as $unArchivo)
            {
                $retorno .= $separador . $unArchivo->getEtiqueta() . self::SEPARADOR_CAMPOS_ARCHIVO . $unArchivo->getRutaCompleta();
                $separador = self::SEPARADOR_VALORES_ARCHIVO;
            }
        }
        
        return $retorno;
    }
    
    public static function extraeCadenaMasLarga ($listado = array())
    {
        $retorno = $listado;

        $code = 'return strlen($a) < strlen($b);';
        $compare = create_function('$a,$b', $code);
        usort($retorno, $compare);

        return $retorno[0];
    }

    /**
     * Dump formateado de todo tipo de variables
     *
     * @param mixed $objeto
     */
    public static function dump ($objeto)
    {
        echo self::abreTag("pre");
        print_r ($objeto);
        echo self::cierraTag("pre");
    }

    /**
     * Apertura de TAG HTML. Opcionalmente concatenado con el valor de $parametros
     *
     * @param String $tag Nombre del TAG HTML
     * @param String $parametros Opcionalmente se concatena junto al <$tag $parametros
     * @return String TAG HTML
     */
    public static function abreTag($tag, $parametros = null)
    {
        $retorno = null;

        if(is_string($tag))
        {
            $tag        = strtolower($tag);
            $retorno    = "<". $tag;

            if(!is_null ($parametros))
            {
                $retorno .= " ". $parametros;
            }

            $retorno .= ">";
        }

        return $retorno;
    }

    /**
     * Cierre de un TAG HTML
     *
     * @param String $tag Nombre del TAG HTML
     * @return String El TAG HTML cerrado
     */
    public static function cierraTag($tag)
    {
        $retorno = null;

        if(is_string($tag))
        {
            $tag     = strtolower ($tag);
            $retorno =  "</". $tag .">";
        }

        return $retorno;
    }


    /**
     * Abre y cierra un TAG HTML
     *
     * @param String $tag Nombre del TAG HTML
     * @return String El TAG HTML completo.
     */
    public static function creaTag($tag, $texto='', $parametros='')
    {
        $retorno = null;

        $retorno = self::abreTag($tag, $parametros);
        $retorno.= $texto;
        $retorno.= self::cierraTag($tag);

        return $retorno;
    }

    /**
     * Genera una cadena MD5 unica aunque se invoque en el mismo segundo
     *
     * @return String La cadena MD5
     */
    public static function generaMD5()
    {
        return md5(  microtime() . $_SERVER['REMOTE_ADDR'] . rand(1,10000000000) );
    }

    /**
     * Genera una cadena de texto alfanumerica aleatoria
     *
     * @return String La cadena generada
     */
    public static function generaClave ($limite = 8)
    {
        $retorno = null;

        $caracteres     = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz1234567890";
        $limiteCadena   = $limite;

        if ($limiteCadena <= 0)
        {
            $limiteCadena = 8;
        }

        $clave = self::ordenarCadenaAleatoriamente($caracteres);
        $clave = substr($clave, 0, $limiteCadena);

        $retorno = $clave;

        return $retorno;
    }

    /**
     * Ordena los caracteres de una cadena de texto aleatoriamente
     *
     * @return String La cadena reordenada
     */
    public static function ordenarCadenaAleatoriamente ($cadena)
    {
        return str_shuffle ($cadena);
    }


    /**
     * Crea un enlace a href
     *
     * @param String $texto El texto que se muestra en el enlace
     * @param String $url La url del enlace
     * @param String $parametros Opcionalmente una lista de parametros que iran a continuacion del <a href='' ...
     * @return String el TAG a completo
     */
    public static function creaEnlaceTexto ($texto, $url, $parametros = "")
    {
        $retorno  = "";

        $retorno .= self::abreTag("a", " href='". $url ."' ". $parametros ." ");
        $retorno .= $texto;
        $retorno .= self::cierraTag("a");

        return $retorno;
    }

    /**
     * Comprueba si una cadena esta vacia o es nula
     *
     * @param String $cadena
     * @return Boolean true si esta vacia o es nula o true en caso contrario
     *
     * TODO: TEST
     */
    public static function cadenaVacia ($cadena)
    {
        $retorno = false;

        $cadenaSinEspacios  = trim($cadena);
        $caracteres         = strlen( $cadenaSinEspacios );
        $esNull             = is_null ($cadenaSinEspacios );

        $retorno = ($caracteres==0 || $esNull);

        return $retorno;
    }

    public static function contieneValor ($cadena)
    {
        return (self::cadenaVacia ($cadena) == false);
    }

    /**
     * Anyade una barra al final de URL en caso de no tenerla
     *
     * @param String $url
     * @param String $caracter opcionalmente podemos indicarle que barra anyadir
     * @return La URL con la barra al final
     *
     * TODO: TEST
     */
    public static function anyadeBarraFinal ($url, $caracter = "/")
    {
        $retorno = $url;

        if(!self::compruebaBarraFinal($url, $caracter))
        {
            $retorno = $url . $caracter;
        }

        return $retorno;
    }

    /**
     * Comprueba si una URL tiene una barra al final
     *
     * @param String $url
     * @param String $caracter La barra que comprobamos si existe al final de la URL
     * @return Boolean true si esta false si no esta la barra
     *
     * TODO: TEST
     */
    public static function compruebaBarraFinal ($url, $caracter= "/")
    {
        $retorno = false;

        $longitud   = strlen ($caracter);
        $final      = substr ($url, strlen($url) - $longitud, $longitud);
        $retorno    = ($final == $caracter);

        return $retorno;
    }

    /**
     * Lee directorios contenidos en un directorio concreto
     *
     * @param String $directorio
     * @return Array listado de directorios encontrados
     */
    public static function getDirectorios ($directorio, $incluirOcultos = false)
    {
        $retorno = array();

        $directorioOrigen = self::anyadeBarraFinal($directorio, DIRECTORY_SEPARATOR);
        $directorioValido = (is_dir($directorioOrigen) && file_exists($directorioOrigen));

        if ($directorioValido)
        {
            $manejador = opendir($directorioOrigen);

            if ($manejador)
            {
                while ( $fichero = readdir($manejador) )
                {
                    $esRaiz       = ($fichero == ".");
                    $esPadre      = ($fichero == "..");
                    $esDirectorio = is_dir($directorioOrigen . $fichero);
                    $esOculto     = preg_match("/^\.{1}[aA-zZ]+.*$/", $fichero);
                    $sinPuntos    = (!$esRaiz && !$esPadre);
                    $continuar    = $sinPuntos && $esDirectorio && ($incluirOcultos || ($esOculto == $incluirOcultos));

                    if($continuar)
                    {
                        array_push ($retorno, $fichero);
                    }
                }

                closedir($manejador);
            }
        }

        return $retorno;
    }

    /**
     * Recorre un directorio completo recursivamente
     *
     * @param String $directorio ( opcional )
     * @return Array contenido completo de todo el arbol del directorio
     *
     * TODO: TEST
     */
    public static function leeDirectorio ($directorio = ".", $incluirOcultos = false)
    {
        $retorno = array();

        $directorioValido = (is_dir($directorio) && file_exists($directorio));

        if ($directorioValido)
        {
            $manejador = opendir($directorio);

            if ($manejador)
            {
                while ( $fichero = readdir($manejador) )
                {
                    $esRaiz     = ($fichero == ".");
                    $esPadre    = ($fichero == "..");
                    $esOculto   = preg_match("/^\.{1}[aA-zZ]+.*$/", $fichero);
                    $sinPuntos  = (!$esRaiz && !$esPadre);
                    $continuar  = $sinPuntos && ($incluirOcultos || ($esOculto == $incluirOcultos));

                    if($continuar)
                    {
                        $directorioActual   = self::anyadeBarraFinal($directorio, DIRECTORY_SEPARATOR);
                        $archivoALeer       = $directorioActual . $fichero;
                        $retorno[$fichero]  = self::leeDirectorio($archivoALeer);
                    }
                }

                closedir($manejador);
            }
        }

        if (!$directorioValido)
        {
            list ($nombre, $retorno) = explode (".", basename ($directorio));
        }

        return $retorno;
    }

    /**
     * Recorre un directorio completo recursivamente y solo deveuelve directorios contenidos
     *
     * @param String $directorio ( opcional )
     * @return Array directorios contenidos
     *
     * TODO: TEST
     */
    public static function leeArbolDirectorio ($directorio = ".", $incluirOcultos = false)
    {
        $retorno = array();

        $directorioValido = (is_dir($directorio) && file_exists($directorio));

        if ($directorioValido)
        {
            $manejador = opendir($directorio);

            if ($manejador)
            {
                while ( $fichero = readdir($manejador) )
                {
                    $esRaiz     = ($fichero == ".");
                    $esPadre    = ($fichero == "..");
                    $esOculto   = preg_match ("/^\.{1}[aA-zZ]+.*$/", $fichero);
                    $sinPuntos  = (!$esRaiz && !$esPadre);
                    $continuar  = $sinPuntos;

                    if($continuar)
                    {
                        $directorioActual   = self::anyadeBarraFinal($directorio, DIRECTORY_SEPARATOR);
                        $archivoALeer       = $directorioActual . $fichero;
                        $esDirectorio       = is_dir ($archivoALeer);
                        $incluirDirectorio  = ( $esDirectorio && ( $incluirOcultos || ($incluirOcultos == $esOculto) ) );

                        if ($incluirDirectorio)
                        {
                            $retorno[$fichero]  = self::leeArbolDirectorio($archivoALeer, $incluirOcultos);
                        }
                    }
                }

                closedir($manejador);
            }
        }

        if (!$directorioValido)
        {
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorno de la ruta absoluta desde donde llamamos al metodo
     *
     * @return String La ruta absoluta
     */
    public static function getRutaAbsoluta()
    {
        return self::anyadeBarraFinal(realpath("."), DIRECTORY_SEPARATOR);
    }

    /**
     * Retorno de la ruta absoluta del nivel anterior desde llamamos al metodo
     *
     * @return String La ruta absoluta
     */
    public static function getRutaAbsolutaParent()
    {
        return self::anyadeBarraFinal(realpath(".."), DIRECTORY_SEPARATOR);
    }

    /**
     * Dandole como argumento una ruta absoluta de un fichero o directorio,
     * te devuelve la ruta relativa a usar via web.
     *
     * @param String $rutaAbsolutaDestino
     * @return String La ruta relativa del archivo o directorio pasado como argumento
     */
    public static function getRutaRelativaDesdeAbsoluta ($rutaAbsolutaDestino)
    {
        $retorno = null;

        if (self::cadenaVacia($rutaAbsolutaDestino))
        {
            return $retorno;
        }

        $rutaAbsolutaDestino = str_replace ("\\", "/", $rutaAbsolutaDestino);

        $rutaLocal      = dirname (realpath ($_SERVER['SCRIPT_FILENAME']) ) . DIRECTORY_SEPARATOR;
        $rutaDestino    = dirname ($rutaAbsolutaDestino) . DIRECTORY_SEPARATOR;
        $esFichero      = preg_match ("/.+\..+/", $rutaAbsolutaDestino);
        $bajarNivel     = true;
        $eregRuta       = "/^". preg_quote($rutaLocal, '/') ."(.+)$/";
        $rutaRelativa   = "./";
        $rutaActual     = "";
        $itemsRuta      = array();

        if (preg_match ($eregRuta, $rutaAbsolutaDestino, $itemsRuta))
        {
            $bajarNivel     = false;
            $rutaRelativa  .= $itemsRuta[1];
        }

        while ($bajarNivel)
        {
            $separador = $rutaRelativa ."../";

            if ($separador != "../")
            {
                $separador = "../";
            }

            $rutaActual    .= $separador;
            $rutaActual     = realpath ($rutaActual) ."/";
            $rutaActual     = str_replace ("\\", "/", $rutaActual);

            $eregRuta       = "/^". preg_quote($rutaActual, "/") ."(.+)$/";
            $rutaRelativa  .= "../";

            if (preg_match ($eregRuta, $rutaAbsolutaDestino, $itemsRuta))
            {
                $bajarNivel     = false;
                $rutaRelativa  .= $itemsRuta[1];
            }
        }

        if (!$esFichero)
        {
            $rutaRelativa = self::anyadeBarraFinal($rutaRelativa, "/");
        }

        $retorno = $rutaRelativa;

        return $retorno;
    }

    /**
     * @param $url
     * @return unknown_type
     */
    public static function creaEnlaceJS ($url)
    {
        $retorno = self::abreTag("script", " type='text/javascript' src='". $url ."'");
        $retorno.= self::cierraTag("script");

        return $retorno;
    }

    /**
     * @param $url
     * @return unknown_type
     */
    public static function creaEnlaceCSS ($url)
    {
        $retorno = self::abreTag("link", " rel='stylesheet' type='text/css' media='screen' href='". $url ."' ");
        $retorno.= self::cierraTag("link");

        return $retorno;
    }
    
    public static function creaContenidoJS ($js)
    {
        $retorno = "<script type='text/javascript'>\n";
        $retorno.= "<!--\n";
        $retorno.= $js."\n";
        $retorno.= "//-->\n";
        $retorno.= "</script>\n";

        return $retorno;
    }

    public static function creaContenidoCSS ($css)
    {
        $retorno = "<style>\n";
        $retorno.= $css."\n";
        $retorno.= "</style>\n";

        return $retorno;
    }

    /**
     * Metodo de soporte para cuando en paginas con codificacion UTF-8
     * los acentos y ciertos caracteres no se ven correctamente.
     *
     * @param $cadena
     * @return String decodificado en ISO88591
     */
    public static function decodificaEnISO88591($cadena)
    {
        return utf8_decode($cadena);
    }

    /**
     * @param $cantidad
     * @return Double formateado para un tipo double BD con separador decimal con caracter '.' y sin separador de miles.
     */
    public static function formateaDinero ($cantidad)
    {
        $retorno = null;

        $contieneDecimales      = (strpos ($cantidad, ",") > 0);
        $cantidadFormateada     = str_replace (",", ".", $cantidad);
        $itemsNumeros           = explode (".", $cantidadFormateada);
        $reconstruirCantidad    = (count ($itemsNumeros) > 2 && $contieneDecimales);

        if ($reconstruirCantidad)
        {
            $parteDecimal       = $itemsNumeros[count ($itemsNumeros) -1];
            $cantidadFormateada = "";
            $topeIteraciones    = count ($itemsNumeros) - 1;

            for ($indice = 0; $indice < $topeIteraciones; $indice++)
            {
                $cantidadFormateada .= $itemsNumeros[$indice];
            }

            $cantidadFormateada .= ".".$parteDecimal;
        }

        $esNumeroValido = (is_float ($cantidadFormateada) || is_numeric ($cantidadFormateada) );

        if (!$esNumeroValido)
        {
            $cantidadFormateada = 0;
        }

        $retorno = $cantidadFormateada;

        return $retorno;

    }
}

// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
// of course PHP has to have the rights to delete the directory
// you specify and all files and folders inside the directory
// ------------------------------------------------------------

// to use this function to totally remove a directory, write:
// recursive_remove_directory('path/to/directory/to/delete');

// to use this function to empty a directory, write:
// recursive_remove_directory('path/to/full_directory',TRUE);

function recursive_remove_directory($directory, $empty=FALSE)
{
    // if the path has a slash at the end we remove it here
    if(substr($directory,-1) == '/')
    {
        $directory = substr($directory,0,-1);
    }
 
    // if the path is not valid or is not a directory ...
    if(!file_exists($directory) || !is_dir($directory))
    {
        // ... we return false and exit the function
        return FALSE;
 
    // ... if the path is not readable
    }elseif(!is_readable($directory))
    {
        // ... we return false and exit the function
        return FALSE;
 
    // ... else if the path is readable
    }else{
 
        // we open the directory
        $handle = opendir($directory);
 
        // and scan through the items inside
        while (FALSE !== ($item = readdir($handle)))
        {
            // if the filepointer is not the current directory
            // or the parent directory
            if($item != '.' && $item != '..')
            {
                // we build the new path to delete
                $path = $directory.'/'.$item;
 
                // if the new path is a directory
                if(is_dir($path)) 
                {
                    // we call this function with the new path
                    recursive_remove_directory($path);
 
                // if the new path is a file
                }else{
                    // we remove the file
                    unlink($path);
                }
            }
        }
        // close the directory
        closedir($handle);
 
        // if the option to empty is not set to true
        if($empty == FALSE)
        {
            // try to delete the now empty directory
            if(!rmdir($directory))
            {
                // return false if not possible
                return FALSE;
            }
        }
        // return success
        return TRUE;
    }
}
// ------------------------------------------------------------


function arrayToObject($d) 
{
    if (is_array($d)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return (object) array_map(__FUNCTION__, $d);
    }
    else {
        // Return object
        return $d;
    }
}

function objectToArray($d) 
{
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
            $d = get_object_vars($d);
        }
 
        if (is_array($d)) {
            /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return array_map(__FUNCTION__, $d);
    }
    else {
        // Return array
        return $d;
    }
}

?>