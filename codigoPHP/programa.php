<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 02/12/2020
 *   Programa
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

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos del formulario
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos

try { // Bloque de código que puede tener excepciones en el objeto PDO
    $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

    $sqlUsuario = "SELECT T01_NumConexiones, T01_DescUsuario, T01_ImagenUsuario FROM T01_Usuario WHERE T01_CodUsuario=:CodUsuario"; 

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

$entradaOK=true; // declaro la variable que determina si esta bien la entrada de los campos introducidos por el usuario


switch ($_COOKIE['idioma']) {
    case 'es':
        $lang_title = "Programa";
        $lang_logoff = "Cerrar Sesion";
        $lang_welcome = "Bienvenido/a ". $descUsuario;
        $lang_numConnections = "Se ha conectado ". $numConexiones ." veces";
        $lang_numConnectionsWelcome = "Esta es la primera vez que se conecta";
        $lang_lastConnection = "Ultima conexion: ". date('d/m/Y H:i:s', $_SESSION['fechaHoraUltimaConexionAnterior']);
        $lang_details = "Detalle";
        $lang_editProfile = "Editar Perfil";
        break;

    case 'en':
        $lang_title = "Program";
        $lang_logoff = "Logoff";
        $lang_welcome = "Welcome ". $descUsuario;
        $lang_numConnections = "You have connected ". $numConexiones ." times";
        $lang_numConnectionsWelcome = "This is the first time you connect";
        $lang_lastConnection = "Last connection: ". date('d/m/Y H:i:s', $_SESSION['fechaHoraUltimaConexionAnterior']);;
        $lang_details = "Detail";
        $lang_editProfile = "Edit Profile";
        break;
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
            main{
                font-size: 2rem;
                text-align: center;
            }
            .buttons-header {
                width: 500px;
                height: 50px;
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }
            .buttons-header *{
                margin: 6px 5px auto 5px;
            }
            
            #fotoPerfil{
                margin: 0 !important;
            }
            .button{
                font-size: 1.2rem;
                background-color: white;
                border-radius: 7px;
                color: #616161;
                font-weight: bold;
                border: 4px solid white;
                margin-right: 6px;
            }
        </style>
    </head>
    <body>
        <header>
            <h1><?php echo $lang_title ?></h1>
            <div class="buttons-header">
                <a href="detalle.php"><button class="button" name="Detalle"> <?php echo $lang_details ?></button></a>
                <a href="editarPerfil.php"><button class="button" name="EditarPefil"> <?php echo $lang_editProfile ?></button></a>
                <?php echo ($imagenUsuario!= null) ?'<img id="fotoPerfil" src = "data:image/png;base64,' . base64_encode($imagenUsuario) . ' alt="Foto de perfil"/>' : "<img id='fotoPerfil' src='../webroot/media/imagen_perfil.png' alt='imagen_perfil'/>";?>
                <form name="logout" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <button class="logout" type="submit" name='cerrarSesion'><?php echo $lang_logoff ?></button> 
                </form>
            </div>
            
        </header>
        <main class="flex-container-align-item-center">
            <article>
                <h2 class="bienvenida"><?php echo $lang_welcome ?> </h2>
                <p><?php echo ($numConexiones>1) ? $lang_numConnections : $lang_numConnectionsWelcome ; ?></p>
                <?php echo ($_SESSION['fechaHoraUltimaConexionAnterior']!=null) ? "<p>".$lang_lastConnection."</p>" : null ; ?>
            </article>
        </main>
    </body>
    <footer class="fixed">
        <a href="http://daw217.ieslossauces.es/" target="_blank"> <img src="../webroot/media/oneandone.png" alt="oneandone icon" width="40"></a>
        <address>  <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> </address>
        <a href="https://github.com/JavierNLSauces/" target="_blank"><img class="github" width="40" src="../webroot/media/github.png" ></a>
    </footer>
</html>