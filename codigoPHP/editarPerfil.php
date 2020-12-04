<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 02/12/2020
 *   Editar perfil
*/

session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217LoginLogoffTema5'])){ // si no se ha logueado le usuario
    header('Location: login.php'); // redirige al login
    exit;
}

if (isset($_REQUEST['cerrarSesion'])) { // si se ha pulsado el boton de Cerrar Sesion
    session_destroy(); // destruye todos los datos asociados a la sesion
    header("Location: login.php"); // redirige al index del tema 5
    exit;
}

if (isset($_REQUEST['Detalle'])) { // si se ha pulsado el boton de Detalle
    header('Location: detalle.php'); // redire¡ige a la misma pagina
    exit;
}

if (isset($_REQUEST['Cancelar'])) { // si se ha pulsado el boton de Detalle
    header('Location: programa.php'); // redire¡ige a la misma pagina
    exit;
}


if (isset($_REQUEST['es'])) { // si se ha pulsado el botton de cerrar sesion
    setcookie('idioma', $_REQUEST['es'], time() + 2592000); // modifica la cookie 'idioma' con el valor recibido del formulario para 30 dias
    header('Location: editarPerfil.php');
    exit;
}

if (isset($_REQUEST['en'])) { // si se ha pulsado el botton de cerrar sesion
    setcookie('idioma', $_REQUEST['en'], time() + 2592000); // modifica la cookie 'idioma' con el valor recibido del formulario para 30 dias
    header('Location: editarPerfil.php');
    exit;
}

switch ($_COOKIE['idioma']) { // dependiendo del valor de la cookie
    case 'es':
        $lang_title = " Editar Perfil";
        $lang_logoff = "Cerrar sesion";
        $lang_user = "Usuario";
        $lang_description = "Descripcion";
        $lang_numConexions = "Numero conexiones";
        $lang_lastConnection = "Ultima conexion";
        $lang_password = "Cambiar contraseña";
        $lang_imageUser = "Imagen Usuario";
        $lang_change = "Editar";
        $lang_cancel = "Cancelar";
        break;

    case 'en':
        $lang_title = " Edit profile";
        $lang_logoff = "Logoff";
        $lang_user = "User";
        $lang_description = "Description";
        $lang_numConexions = "Number connections";
        $lang_lastConnection = "Last Connection";
        $lang_password = "Change password";
        $lang_imageUser = "User image";
        $lang_change = "Edit";
        $lang_cancel = "Cancel";
        break;
}

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos del formulario
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos

try { // Bloque de código que puede tener excepciones en el objeto PDO
    $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

    $sqlUsuario = "SELECT * FROM T01_Usuario WHERE T01_CodUsuario=:CodUsuario"; 

    $consultaUsuario = $miDB->prepare($sqlUsuario); // prepara la consulta
    $parametros = [':CodUsuario' => $_SESSION['usuarioDAW217LoginLogoffTema5'] // creo el array de parametros con el valor de los parametros de la consulta
                  ];

    $consultaUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
    
    $oUsuario = $consultaUsuario->fetchObject(); // guarda en la variable un objeto con los datos solicitados en la consulta
    
    $numConexiones = $oUsuario->T01_NumConexiones; // variable que tiene el numero de conexiones sacado de la base de datos
    $descUsuario = $oUsuario->T01_DescUsuario; // variable que tiene la descripcion del usuario sacado de la base de datos
    $imagenUsuario = $oUsuario->T01_ImagenUsuario;

} catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
    echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
    echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
    echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
    die(); // Finalizo el script
} finally { // codigo que se ejecuta haya o no errores
    unset($miDB); // destruyo la variable 
}

define("OBLIGATORIO", 1); // defino e inicializo la constante a 1 para los campos que son obligatorios

$entradaOK=true; // declaro la variable que determina si esta bien la entrada de los campos introducidos por el usuario


$aErrores = [ //declaro e inicializo el array de errores
    'DescUsuario' => null,
    'ImagenUsuario' => null
];


if (isset($_REQUEST["Editar"])) { // comprueba que el usuario le ha dado a al boton de IniciarSesion y valida la entrada de todos los campos
    $aErrores['DescUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescUsuario'], 255, 3, OBLIGATORIO); // comprueba que la entrada del codigo de usuario es correcta

    if (!empty($_FILES['ImagenUsuario']['name'])) { // si se ha subido un archvo
        if ($_FILES['ImagenUsuario']['size'] < 5242880) { // si el tamaño del archivo es menor de 5MB
            if (($_FILES["ImagenUsuario"]["type"] == "image/jpeg") || ($_FILES["ImagenUsuario"]["type"] == "image/jpg") || ($_FILES["ImagenUsuario"]["type"] == "image/png")) { // si el tipo del archivo es correcto
                $imagenUsuario = file_get_contents($_FILES["ImagenUsuario"]['tmp_name']); // pasa el contenido del archivo a la variable
            } else {
                $aErrores['ImagenUsuario'] = "Los formatos admitidos son : jpeg,jpg,png";
            }
        } else {
            $aErrores['ImagenUsuario'] = "La imagen no puede ocupar mas de 5MB";
        }
        
        
    }
    if ($aErrores['DescUsuario'] != null) { // compruebo si hay algun mensaje de error en algun campo
            $entradaOK = false; // le doy el valor false a $entradaOK
            $_REQUEST['DescUsuario'] = ""; // si hay algun campo que tenga mensaje de error pongo $_REQUEST a null
        }
        
        if ($aErrores['ImagenUsuario'] != null) { // compruebo si hay algun mensaje de error en algun campo
            $entradaOK = false; // le doy el valor false a $entradaOK
        }
} else { // si el usuario no le ha dado al boton de enviar
    $entradaOK = false; // le doy el valor false a $entradaOK
}

if ($entradaOK) { // si la entrada esta bien recojo los valores introducidos y hago su tratamiento

    try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $sqlUpdateDescUsuario = "UPDATE T01_Usuario SET T01_DescUsuario=:DescUsuario, T01_ImagenUsuario=:PathImagenUsuario WHERE T01_CodUsuario=:CodUsuario" ;

        $consultaUpdateDescUsuario = $miDB->prepare($sqlUpdateDescUsuario); // prepara la consulta
        $parametros = [':DescUsuario' => $_REQUEST['DescUsuario'],// creo el array de parametros con el valor de los parametros de la consulta
                       ':PathImagenUsuario' => $imagenUsuario,
                       ':CodUsuario' => $_SESSION['usuarioDAW217LoginLogoffTema5']
                       ]; 

        $consultaUpdateDescUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
        
        
        header('Location: programa.php'); // redirige al programa
        exit;
        
    } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
        echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
        echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
        echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
        die(); // Finalizo el script
    } finally { // codigo que se ejecuta haya o no errores
        unset($miDB); // destruyo la variable 
    }
    
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $lang_title ?></title>
        <meta name="viewport"   content="width=device-width, initial-scale=1.0">
        <meta name="author"     content="Javier Nieto Lorenzo">
        <meta name="robots"     content="index, follow">      
        <link rel="stylesheet"  href="../webroot/css/estilos.css"       type="text/css" >
        <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
        <style>
            header>img{
                position: absolute;
                right: 160px;
                width: 80px;
            }
        </style>
    </head>
    <body>
        <header>
            <h1><?php echo $lang_title ?></h1>
            
            <form name="logout" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <button class="logout" type="submit" name='cerrarSesion'><?php echo $lang_logoff ?></button>
            </form>
            
        </header>
        <main class="flex-container-align-item-center">
            <form name="editarPerfil" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">

                <div>
                    <label for="CodUsuario"><?php echo $lang_user ?></label>
                    <input class="required" type="text" id="CodUsuario" name="CodUsuario" value="<?php echo $_SESSION['usuarioDAW217LoginLogoffTema5']; ?>" readonly>
                </div>
                <div>
                    <label for="DescUsuario"><?php echo $lang_description ?></label>
                    <input style="width: 240px;" class="required" type="text" id="DescUsuario" placeholder="<?php echo $lang_description ?>" name="DescUsuario" value="<?php
                        echo (isset($_REQUEST['DescUsuario'])) ? $_REQUEST['DescUsuario'] : $descUsuario; 
                        ?>">
                </div>
                <?php
                    echo ($aErrores['DescUsuario']!=null) ? "<span style='color:#FF0000'>".$aErrores['DescUsuario']."</span>" : null;// si el campo es erroneo se muestra un mensaje de error
                ?>
                
                <div>
                    <label for="NumConexiones"><?php echo $lang_numConexions ?></label>
                    <input style="width: 100px;" class="required" type="text" id="NumConexiones" name="NumConexiones" value="<?php echo $numConexiones ?>" readonly>
                </div>
                
                <?php if($_SESSION['fechaHoraUltimaConexionAnterior']!=null){ ?>
                <div>
                    <label for="UltimaConexion"><?php echo $lang_lastConnection ?></label>
                    <input style="width: 240px;;" class="required" type="text" id="UltimaConexion" name="UltimaConexion" value="<?php echo date('d/m/Y H:i:s', $_SESSION['fechaHoraUltimaConexionAnterior']) ?>" readonly>
                </div>
                <?php } ?>
                
                <div style="width: 500px";>
                    <label for="ImagenUsuario"><?php echo $lang_imageUser ?></label>
                    <input style="width: 390px;margin: auto; font-size: 1rem" class="required" type="file" id="ImagenUsuario" name="ImagenUsuario" value="">
                </div>
                <?php
                    echo ($aErrores['ImagenUsuario']!=null) ? "<span style='color:#FF0000'>".$aErrores['ImagenUsuario']."</span>" : null;// si el campo es erroneo se muestra un mensaje de error
                ?>
                
                <div>
                    <a class="registrarse" href="cambiarPassword.php"><?php echo $lang_password ?></a>
                    <button style="margin:auto;" class="button" type="submit" name="Editar"><?php echo $lang_change ?></button>
                    <button style="margin:auto; margin-top: 5px;" class="button" name="Cancelar"><?php echo $lang_cancel ?></button>
                </div>

                </form>
        </main>
    </body>
    <footer class="fixed">
        <a href="http://daw217.ieslossauces.es/" target="_blank"> <img src="../webroot/media/oneandone.png" alt="oneandone icon" width="40"></a>
        <address>  <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> </address>
        <a href="https://github.com/JavierNLSauces/" target="_blank"><img class="github" width="40" src="../webroot/media/github.png" ></a>
    </footer>
</html>