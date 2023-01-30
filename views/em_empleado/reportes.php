<?php /** @var \gamboamartin\empleado\models\em_empleado $controlador controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates . "head/lista/title.php"; ?>
                <?php include (new views())->ruta_templates . "mensajes.php"; ?>
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <?php include (new views())->ruta_templates . "head/subtitulo.php"; ?>
                    <div class="filtros">

                        <h3>Estimado usuario, por favor seleccione una opción de busqueda:</h3>

                        <div class="categorias">
                            <h4>Seleccione una categoría</h4>
                            <div class="lista_categorias" style="display: flex; justify-content:space-around">
                                <input type="checkbox" class="filter-checkbox" value="Programador"/> Programador
                                <input type="checkbox" class="filter-checkbox" value="Docente"/> Docente
                            </div>
                        </div>
                        <div class="inputs">
                            <!--<div class="inputs_filter" style="display: flex; flex-direction: column">
                                <label for="alianzas">Alianza</label>
                                <select name="alianzas" id="alianzas">
                                    <option value="">--Please choose an option--</option>
                                    <option value="dog">Dog</option>
                                    <option value="cat">Cat</option>
                                    <option value="hamster">Hamster</option>
                                    <option value="parrot">Parrot</option>
                                    <option value="spider">Spider</option>
                                    <option value="goldfish">Goldfish</option>
                                </select>
                            </div>
                            <div class="inputs_filter" style="display: flex; flex-direction: column">
                                <label for="registros_patronales">Registros Patronales</label>
                                <select name="registros_patronales" id="registros_patronales">
                                    <option value="">--Please choose an option--</option>
                                    <option value="dog">Dog</option>
                                    <option value="cat">Cat</option>
                                    <option value="hamster">Hamster</option>
                                    <option value="parrot">Parrot</option>
                                    <option value="spider">Spider</option>
                                    <option value="goldfish">Goldfish</option>
                                </select>
                            </div>-->
                            <div class="inputs_filter" style="display: flex; flex-direction: column">
                                <label >Rango Fechas</label>
                                <div class="fechas">
                                    <input type="date" id="remunerado" name="fecha_inicio">
                                    <input type="date" id="remunerado" name="fecha_fin">
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="table-head" style="display: flex; justify-content: space-between; align-items: center ">

                        <div class="botones" style="display: flex; justify-content: space-around; align-items: center">
                            <form method="post" action="<?php echo $controlador->link_em_empleado_exportar; ?> "
                                  class="form-additional" id="form_export">
                            </form>
                            <button type="submit" class="btn btn-success" name="btn_action_next"
                                    style="border-radius: 5px" value="exportar" form="form_export">
                                Exportar
                            </button>
                        </div>
                    </div>
                    <table id="em_empleado" class="datatables table table-striped "></table>
                </div>

            </div>
        </div>
    </div>
</main>







