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

if (isset($_REQUEST['es'])) { // si se ha pulsado el botton de cerrar sesion
    setcookie('idioma', $_REQUEST['es'], time() + 2592000); // modifica la cookie 'idioma' con el valor recibido del formulario para 30 dias
    header('Location: programa.php');
    exit;
}

if (isset($_REQUEST['en'])) { // si se ha pulsado el botton de cerrar sesion
    setcookie('idioma', $_REQUEST['en'], time() + 2592000); // modifica la cookie 'idioma' con el valor recibido del formulario para 30 dias
    header('Location: programa.php');
    exit;
}

switch ($_COOKIE['idioma']) {
    case 'es':
        $saludo = "Bienvenido/a";
        break;

    case 'en':
        $saludo = "Welcome";
        break;
}

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos del formulario
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos

try { // Bloque de código que puede tener excepciones en el objeto PDO
    $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

    $sqlUsuario = "SELECT T01_NumConexiones, T01_DescUsuario FROM T01_Usuario WHERE T01_CodUsuario=:CodUsuario"; 

    $consultaUsuario = $miDB->prepare($sqlUsuario); // prepara la consulta
    $parametros = [':CodUsuario' => $_SESSION['usuarioDAW217LoginLogoffTema5'] // creo el array de parametros con el valor de los parametros de la consulta
                  ];

    $consultaUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
    
    $oUsuario = $consultaUsuario->fetchObject(); // guarda en la variable un objeto con los datos solicitados en la consulta
    
    $numConexiones = $oUsuario->T01_NumConexiones; // variable que tiene el numero de conexiones sacado de la base de datos
    $descUsuario = $oUsuario->T01_DescUsuario; // variable que tiene la descripcion del usuario sacado de la base de datos

} catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
    echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
    echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
    echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
    die(); // Finalizo el script
} finally { // codigo que se ejecuta haya o no errores
    unset($miDB); // destruyo la variable 
}

$entradaOK=true; // declaro la variable que determina si esta bien la entrada de los campos introducidos por el usuario


if (isset($_REQUEST['cerrarSesion'])) { // si se ha pulsado el boton de Cerrar Sesion
    session_destroy(); // destruye todos los datos asociados a la sesion
    header("Location: login.php"); // redirige al index del tema 5
    exit;
}

if (isset($_REQUEST['Detalle'])) { // si se ha pulsado el boton de Detalle
    header('Location: detalle.php'); // redire¡ige a la misma pagina
    exit;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Programa</title>
        <meta name="viewport"   content="width=device-width, initial-scale=1.0">
        <meta name="author"     content="Javier Nieto Lorenzo">
        <meta name="robots"     content="index, follow">      
        <link rel="stylesheet"  href="../webroot/css/estilos.css"       type="text/css" >
        <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
        <style>
            form[name="formularioIdioma"]{
                position: absolute;
                top: 52px;
                right: 0;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>Programa</h1>
        </header>
        <main>
            <form name="logout" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <button class="logout" type="submit" name='cerrarSesion'>Cerrar Sesion</button>
            </form>
            <form name="formularioIdioma" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <button class="idioma" type="submit" name="es" value="es"> Castellano</button>
                <button class="idioma" type="submit" name="en" value="en"> English</button>
            </form>
            <h2 class="bienvenida"><?php echo $saludo . " " . $descUsuario; ?> </h2>
            <p><?php echo ($numConexiones>1) ? "Se ha conectado ". $numConexiones ." veces" : "Esta es la primera vez que se conecta" ; ?></p>
            <?php echo ($_SESSION['fechaHoraUltimaConexionAnterior']!=null) ? "<p>Ultima conexion: ". date('d/m/Y H:i:s', $_SESSION['fechaHoraUltimaConexionAnterior'])."</p>" : null ; ?>
            
            <a href="detalle.php"><button class="button" name="Detalle"> Detalle</button></a>
            
        </main>
    </body>
    <footer class="fixed">
        <address> <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> <a href="https://github.com/JavierNLSauces/"><img class="github" width="40" src="../webroot/media/github.png" ></a></address>
    </footer>
</html>