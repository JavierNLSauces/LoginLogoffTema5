<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 01/12/2020
 *   Programa
*/


session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217LoginLogoffTema5'])){ // si no se ha logueado le usuario
    header('Location: login.php'); // redirige al login
    exit;
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

$erroresIdioma=null; //declaro e inicializo la variable de errores

$idioma=null; //declaro e inicializo la variable del idioma

if (isset($_REQUEST['cerrarSesion']) || isset($_REQUEST['Detalle'])) { // si se ha pulsado el botton de cerrar sesion
    if ($_COOKIE['idioma'] != $_REQUEST['idioma']) { // si la cookie 'idioma' tieneel distinto valor del que se ha enviado en el formulario
        $erroresIdioma = validacionFormularios::validarElementoEnLista($_REQUEST['idioma'], ['es', 'en', 'fr']); // valido el campo que ha seleccionado el usuario

        if ($erroresIdioma != null) { // si hay algun mensaje de error 
            $entradaOK = false; // le doy el valor false a $entradaOK
        }

        if ($entradaOK) {
            $idioma = $_REQUEST['idioma']; // asigno a la variable el valor recibido del formulario
            setcookie('idioma', $idioma,time()+2592000); // modifica la cookie 'idioma' con el valor recibido del formulario para 30 dias

            if ($idioma == "en") { // si el idioma seleccionado es 'en'
                setcookie('saludo', 'Hello',time()+2592000);  // modifica el valor de la cookie 'saludo' para 30 dias
            }
            if ($idioma == "fr") { // si el idioma seleccionado es 'fr'
                setcookie('saludo', 'Salut',time()+2592000);  // modifica el valor de la cookie 'saludo'  para 30 dias
            }
            if ($idioma == "es") { // si el idioma seleccionado es 'es'
                setcookie('saludo', 'Hola',time()+2592000);  // modifica el valor de la cookie 'saludo'  para 30 dias
            }
        }
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
    </head>
    <body>
        <header>
            <h1>Programa</h1>
        </header>
        <main>
            <h2 class="bienvenida"><?php echo $_COOKIE['saludo'] . " " . $descUsuario; ?> </h2>
            <p><?php echo ($numConexiones>1) ? "Se ha conectado ". $numConexiones ." veces" : "Esta es la primera vez que se conecta" ; ?></p>
            <?php echo ($_SESSION['ultimaConexionDAW217LoginLogoffTema5']!=null) ? "<p>Ultima conexion: ". date('d/m/Y H:m:s', $_SESSION['ultimaConexionDAW217LoginLogoffTema5'])."</p>" : null ; ?>
            <form name="formularioIdioma" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div>
                    <label for ="idioma">Idioma: </label>
                    <select id="idioma" name="idioma">
                        <option value="es" <?php echo (($_COOKIE['idioma']) == 'es') ? 'selected' : null; ?> >Castellano</option>
                        <option value="en" <?php echo (($_COOKIE['idioma']) == 'en') ? 'selected' : null; ?> >English</option>
                        <option value="fr" <?php echo (($_COOKIE['idioma']) == 'fr') ? 'selected' : null; ?> >Français</option>
                    </select>
                    <?php
                        echo(!is_null($erroresIdioma)) ? "<span style='color:#FF0000'>" . $erroresIdioma . "</span>" : null;   // si el campo es erroneo se muestra un mensaje de error
                    ?>
                </div>
                <div>
                    <button class="button" type="submit" name="Detalle"> Detalle</button>
                    <button class="logout" type="submit" name='cerrarSesion'>Cerrar Sesion</button>
                </div>
            </form>
        </main>
    </body>
    <footer class="fixed">
        <address> <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> <a href="https://github.com/JavierNLSauces/"><img class="github" width="40" src="../webroot/media/github.png" ></a></address>
    </footer>
</html>