<!--Ejercicio Propuesto 3.2.4-->

<!--Modifica el ejercicio sobre consultas preparadas que realizaste con la extensión MySQLi, 
el que modificaba el número de unidades de un producto en las distintas tiendas, 
para que utilice ahora la extensión PDO.-->

<?php
//hacemos la conexión, sería buena idea hacerla en un archivo aparte
//y utilizar 'require' o 'require_once' por ejemplo
$host = "localhost";
$db = "proyecto";
$user = "gestor";
$pass = "secreto";
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$conProyecto = new PDO($dsn, $user, $pass);
$conProyecto->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

function pintarBoton() {
    echo "<a href='{$_SERVER['PHP_SELF']}' class='btn btn-success mb-2'>Consultar Otro Artículo</a>";
}
?>

<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- css para usar Bootstrap -->
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <title>Ejercicio T3: Consultas preparadas en PDO</title>
    </head>
    <body style="background: antiquewhite">
        <h3 class="text-center mt-2 font-weight-bold">Consultas preparadas en PDO</h3>
        <div class="container mt-3">
            <?php
            if (isset($_POST['enviar'])) {
                $codigo = $_POST['producto'];
                $consulta = "select unidades, tienda, producto, tiendas.nombre as nombreTienda from stocks, tiendas where tienda=tiendas.id AND producto=:prod";
                $consulta1 = "select nombre , nombre_corto from productos where id=:id";
                $stmt = $conProyecto->prepare($consulta);
                $stmt1 = $conProyecto->prepare($consulta1);
                $stmt->execute([':prod' => $codigo]);
                $stmt1->execute([':id' => $codigo]);
                $fila = $stmt1->fetch(PDO::FETCH_OBJ);
                //solo nos devuelve una fila es innecesario el while
                echo "<h4 class='mt-3 mb-3 text-center '>Unidades del Producto: ";
                echo "$fila->nombre ($fila->nombre_corto)";
                echo "</h4>";
                pintarBoton();
                echo "<table class='table table-striped table-dark'>";
                echo "<thead>";
                echo "<tr class='font-weight-bold'><th class='text-center'>Nombre Tienda</th>";
                echo "<th>Unidades</th><th class='text-center'>Acciones</th></tr>";
                echo "</thead>";
                echo "<tbody>";
                while ($filas = $stmt->fetch(PDO::FETCH_OBJ)) {
                    echo "<tr class='text-center'><td>$filas->nombreTienda</td>";
                    echo "<td class='text-center m-auto'>";
//creamos el formulario para modificar stock
                    echo "<form name='a' action='{$_SERVER['PHP_SELF']}' method='POST' class='form-inline'>";
                    echo "<input type='number' class='form-control' step='1' min='0' name='stock' value='{$filas->unidades}'>";
                    echo "<input type='hidden' name='ct' value='{$filas->tienda}'>";
                    echo "<input type='hidden' name='cp' value='{$filas->producto}'>";
                    echo "</td><td>";
                    echo "<input type='submit' class='btn btn-warning ml-2' name='enviar1' value='Actualizar'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
//Cerrramos conexiones.
                $stmt = null;
                $stmt1 = null;
                $conProyecto = null;
            } elseif (isset($_POST['enviar1'])) {
                $codTienda = $_POST['ct'];
                $codProducto = $_POST['cp'];
                $unidades = $_POST['stock'];
                $update = "update stocks set unidades=:u where producto=:p AND tienda=:t";
                $stmt = $conProyecto->prepare($update);
                $stmt->execute([':u' => $unidades, ':p' => $codProducto, ':t' => $codTienda]);
                echo "<p class='font-weight-bold text-success mt-3'>Unidades Actualizadas Correctamente</p>";
                $stmt = null;
                $conProyecto = null;
                pintarBoton();
            } else { //no hemos enviado ningún formulario
                ?>
                <form name="f1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="form-group">
                        <label for="p" class="font-weight-bold">Elige un producto</label>
                        <select class="form-control" id="p" name="producto">
                            <?php
                            $consulta = "select id, nombre, nombre_corto from productos order by nombre";
                            $stmt = $conProyecto->prepare($consulta);
                            $stmt->execute();

                            while ($filas = $stmt->fetch(PDO::FETCH_OBJ)) {
                                echo "<option value='{$filas->id}'>$filas->nombre</option>";
                            }

//cerramos las conexiones.
                            $stmt = null;
                            $conProyecto = null;
                            ?>               
                        </select>
                    </div>
                    <div class="mt-2">
                        <input type="submit" class="btn btn-info mr-3" value="Consultar Stock" name="enviar">
                    </div>
                </form>
            </div>
        <?php } ?>
    </body>
</html>
