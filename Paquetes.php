<?php
session_start();
require_once("../General.php");
require_once('../../model/Model.php');
require_once('../../model/WebModel.php');
$model = new Model();
$wmodel = new WebModel();
$wmodel->Head();

$arrBgColor = array();
   if ($cssGrid == 0){
	 $arrBgColor[0] = "#FFFFFF";
	 $arrBgColor[1] = "#FAFAFA";
	 $arrBgColor[2] = "#FEF7E7";
   }else if ($cssGrid == 1){
	 $arrBgColor[0] = "#FFFFFF";
	 $arrBgColor[1] = "#000000";
	 $arrBgColor[2] = "#FEF7E7";
   }else if ($cssGrid == 2){
	 $arrBgColor[0] = "#FFFFFF";
	 $arrBgColor[1] = "#DADCEF";
	 $arrBgColor[2] = "#003366"; // color de la cabecera de la tabla
   }

if (!isset($_REQUEST["operacion"])) {
	clearSessionObject();
}

?>
<link rel="stylesheet" href="<?php echo $_SESSION['path'] ?>/comun/js/bower_components/chosen/chosen.min.css" />
<link rel="stylesheet" href="<?php echo $_SESSION['path'] ?>/comun/jquery-ui/css/redmond/jquery-ui-1.10.2.custom.min.css" />
<link rel="stylesheet" href="<?php echo $_SESSION['path'] ?>/comun/lou-multi-select/css/multi-select.dist.css" />

<script src="<?php echo $_SESSION['path'] ?>/comun/jquery-ui/js/jquery-1.9.1.js"></script>
<script src="<?php echo $_SESSION['path'] ?>/comun/jquery-ui/js/jquery-ui-1.10.2.custom.js"></script>
<script src="<?php echo $_SESSION['path'] ?>/comun/lou-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo $_SESSION['path'] ?>/comun/js/bower_components/chosen/chosen.jquery.min.js"></script>

<script language="javascript">
$(function(){
    
    $('.chosen-select').chosen({
			width: '100%'
	});
    
    $(".chosen-search-input").attr("name","searchEnfasis");  //Asigna un nombre en el campo type text correspondiente al plugin
    
    $(".chosen-select").chosen();

    var valueEnfasis = "<?php echo $_SESSION['tipo_enfasis'];?>" //Trale los valores correspondientes a la tabla tipo_enfasis
    //console.log('ddd'+valueEnfasis);
    if(valueEnfasis!=''){
                var datos = valueEnfasis.split ('&&'); 
                $("#enfasis").val(valueEnfasis); //Asigna el valor de los datos en el campo hidden enfasis ya que se pierden los datos por la session
				$(".chosen-select").val(datos).trigger("chosen:updated"); //Selecciona en el select multiple los datos traidos de la base de datos
	
    }

});
    // triggers when whole value changed
   // console.log(e);

function ConsultaManual() {
    showWindowFind('codigo,nombre,tipo', 'sis_manual', '', 'Codigo,Nombre', '80,300', 'codigo_precio#manual#tipo',
        'Manuales');
}

function buscarPaquete() {
    const codigoEmp=document.getElementById('codigo_empresa').value;
    if(codigoEmp!=''){
        console.log(codigoEmp);
        showWindowFind('codigo,nombre', 'listaPaquete', "codigo_empresa=!"+codigoEmp+"!", 'Codigo,Nombre', '80,300', 'codigo_paquete#paquete', 'Paquete','getPaquete()');
    }else{
        alert("Por Favor Seleccione un Contrato");
    }
    // showWindowFind('codigo,nombre,categoria,nombre_empresa,tipo_evaluacion,tipo_enfasis,cargo,codigo_empresa,idcategoria','listaPaquete','','Codigo,Nombre','80,300','codigo_paquete#paquete#categoria#nombre_empresa#tipo_evaluacion#tipo_enfasis#cargo#codigo_empresa#idcategoria','Paquete');

}

function ConsultaProc() {
    codigo = document.f.codigo_paquete.value;
    if (codigo == "") {
        alert("No ha seleccionado un profesiograma");
        return;
    }
    showWindowFind('nombreve,codigo,fuente', 'sis_proc', '', 'Nombre,Codigo', '450,80', 'nombre#codigo#cod_servicio',
        'Lista de Procedimientos')
}

function buscarPrecios(){
	
	if(getValue("codigo_empresa") != ""){
		showWindowFind('nombre,codigo','sis_manual',"codigo = !"+getValue("codigo_empresa")+"!",'Nombre, Codigo','350,80','nombre_precio#codigo_precio','Precios');
	}else{
		alert('Seleccione un Contrato');
	}
	
	
}

function Imprimir() {
    if(getValue("codigo_paquete") != ""){
        invocarReporte("reporte_paquete.php?numero=" + getValue("codigo_paquete"), "conf");
        
    }
    if(getValue("codigo_empresa") != "",getValue("codigo_precio") != ""){
       
        invocarReporte("reporte_paquete_empresa.php?empresa=" + getValue("codigo_empresa")+"&manual=" + getValue("codigo_precio"), "conf");
       

    }
    else{
        invocarReporte("reporte_paquete_todas_las_empresas.php?", "conf");
    }
  
}


function ejecutarProc() {
    document.f.reset();
    document.f.cantidad.value = 1;
    ConsultaProc()
}
function getPaquete(){
    const codigo = document.getElementById('codigo_paquete');
    if (codigo != null) {
        if(codigo.value != ''){
            document.f.action += '?operacion=abrir_paq'; document.f.submit();      
        }else{
            alert('Por Favor Seleccione Un Paquete');
        }
    }
    disableInput();
}
function disableInputs(){   //Función que desahabilita los campos

       // document.getElementById("nombre_empresa").readOnly = true;
       // document.getElementById('nombre_empresa').style.background='#dddddd';
        document.getElementById("nombre_Contrato").readOnly = true;
        document.getElementById('nombre_Contrato').style.background='#dddddd';
        document.getElementById("paquete").readOnly = true;
        document.getElementById('paquete').style.background='#dddddd';
        

        //SELECT OPTION
        $('#categoria option:not(:selected)').attr('disabled',true);
        $('#tipo_enfasis option:not(:selected)').attr('disabled',true);
}
</script>

<style>
    #categoria #tipo_evaluacion{
        width: 50%;
    }
</style>
<form method="post" name="f" id="f" action="CtrlPaquetes.php">
    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td height="12" valign="middle"></td>
        </tr>
        <tr>
            <td align="center" valign="top">
                <table width="100%" border="0" cellpadding="2" cellspacing="2" class="LSPRESSE">
                    <tr>
                        <th height="19" align="left" background="../../imagenes/bgtop.gif"
                            class="letraCaptionNegrita" scope="col">&nbsp;&nbsp;Formulaci&oacute;n de Profesiogramas
                        </th>
                    </tr>
                    <tr>
                        <th height="23" align="left" scope="col" class="nomGrupo">Datos del Profesiograma </th>
                    </tr>
                    <tr>
                        <th height="125" valign="middle" scope="col">
                            <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
                                <tr>
                                    <td height="86">
                                        <table width="98%" border="0" align="center" cellpadding="2" cellspacing="2">
                                      
                                            <tr>
                                                <td width="135" class="letraDisplay">Contrato</td>
                                                <td width="427"><input name="nombre_Contrato" type="text" class="input"
                                                        id="nombre_Contrato"
                                                        value="<?php echo $_SESSION["nombre_Contrato"]?>" size="47"
                                                        readonly="yes" title="Contrato" />
                                                    <a
                                                        href="javascript:showWindowFind('nombre,codigo,empresa,manual','contratos','','Nombre, Codigo,Empresa,Manual','450,80','nombre_Contrato#codigo_contrato#codigo_empresa#codigo_precio','empresa')">
                                                        <?php if (empty($_SESSION['codigo_contrato'])) { ?>
                                                        <img src="../../imagenes/lupa2.gif" alt="Buscar Contrato"
                                                            border="0" />
                                                        <?php } ?>
                                                    </a></td>
                                            </tr>
                                        
                                         
                                            
                                            <tr>
                                                <td class="letraDisplay" width="135">Nombre</td>
                                                <td class="letra"><input name="paquete" type="text" class="input"
                                                        id="paquete" value="<?php echo $_SESSION["paquete"]; ?>"
                                                        size="50"
                                                        onkeypress="return validFormat(this,event,'Mayus','nextFocus(\'costo\')')" />
                                                </td>
                                            </tr>
                                            <tr>

                                            <input type="hidden" name="idcategoria" id="idcategoria"
                                                value="<?php  echo $_SESSION['idcategoria'] ?>" />
                                            
                                            <tr>
                                                <td class="letraDisplay">Categor&iacute;a / Area</td>
                                                <td class="letra">
                                                    <select name="categoria" title="categoria" class="input selectInput"
                                                        id="categoria">
                                                        <option value="">[seleccione]</option>
                                                            <?php echo $model->crearCombo("categoria_cargo","idcategoria","nombre","estado ='on'", $_SESSION['categoria']); ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                
                                                
                                            <input type="hidden" name="codigo_contrato" id="codigo_contrato"
                                                    value="<?php  echo $_SESSION['codigo_contrato'] ?>" />
                                                <input type="hidden" name="codigo_empresa" id="codigo_empresa"
                                                    value="<?php  echo $_SESSION['codigo_empresa'] ?>" />
                                                <input type="hidden" name="codigo_precio" id="codigo_precio"
                                                    value="<?php  echo $_SESSION['codigo_precio'] ?>" />
                                                    
                                                    <input type="hidden" name="codigo_paquete" id="codigo_paquete"
                                                    value="<?php  echo $_SESSION['codigo_paquete'] ?>" />
                                                    
                                                

                                                <!-- <td class="letraDisplay">Tipo Evaluaci&oacute;n </td>
                                                <td class="letra">
                                                    <select name="tipo_evaluacion" id="tipo_evaluacion"
                                                        class="input selectInput" title="Tipo de Evaluacion">
                                                        <option value="">[Seleccione]</option>
                                                        <?php echo $model->crearCombo("tipo_evaluacion WITH (nolock)","nombre","nombre","estado ='on'", $_SESSION['tipo_evaluacion']); ?>
                                                    </select>
                                                </td> -->
                                            </tr>

                                            <tr>
												<td align="left" class="letraDisplay">&Eacute;nfasis</td>
												<td colspan="6" class="letraDisplay">
													<select name="tipo_enfasis[]" title="tipo_enfasis" ata-placeholder="Selecciona las opciones" class="chosen-select" id="tipo_enfasis" multiple>
													<?php echo $model->crearCombo("tipo_enfasis WITH (nolock)","nombre","nombre"," estado = 'on' order by orden ",$estudiop != -1 ? $maestro->enfasis : $_REQUEST['enfasis']); ?>
                                                    </select>
                                                    <input type="hidden" name="enfasis" id="enfasis" value="<?php  echo $_SESSION['tipo_enfasis'] ?>" />
												</td>
                                            </tr>
											
											<tr>
												<td align="left" class="letraDisplay">Valor</td>
												<td colspan="6" class="letraDisplay">
													$ <?php  echo number_format($_SESSION ["valor_paquete"]); ?> 
												</td>
                                            </tr>
											
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td><br>
                                        <table width="100%" cellpadding="2" cellspacing="1">
                                            <tr>
                                                <td align="center" class="LSBOTABLE"><a
                                                        href="CtrlPaquetes.php?operacion=nuevo_paq">Nuevo </a></td>
                                                <td align="center" class="LSBOTABLE"><a
                                                        href="javascript:document.f.action += '?operacion=guardar_paq&codigo_empresa=<?php  echo $_SESSION['codigo_empresa'] ?>&idmanual=<?php  echo $_SESSION['codigo_precio']?>&paquete=<?php echo $_SESSION["paquete"]; ?>&mensaje()'; document.f.submit();">Guardar
                                                    </a></td>
                                                <td align="center" class="LSBOTABLE"><a
                                                        href="javascript:buscarPaquete()">Buscar </a> </td>
                                                <td align="center" class="LSBOTABLE"><a
                                                       href="javascript:Imprimir();">Imprimir</a></td>
                                                
                                                <td align="center" class="LSBOTABLE"><a
                                                        href="javascript:document.f.action += '?operacion=eliminar_paq&idmanual=<?php  echo $_SESSION['codigo_precio'] ?>&codigo_paquete=<?php  echo $_SESSION['codigo_paquete'] ?>'; document.f.submit();">Eliminar
                                                    </a></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </th>
                    </tr>
                    <tr>
                        <th height="25" align="left" class="nomGrupo">Configuraci&oacute;n del Profesiograma </th>
                    </tr>
                    <tr>
                        <th height="98" scope="col">
                            <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2">
                                <tr>
                                    <td width="147" class="letraDisplay"><?php echo $i18n->translate('cups'); ?></td>
                                    <td colspan="2"><input name="codigo" type="text" class="input" id="codigo" size="10"
                                            readonly="readonly" />
                                        <a href="javascript:ejecutarProc()"><img src="../../imagenes/lupa2.gif"
                                                border="0" /></a></td>
                                </tr>
                                <tr>
                                    <td class="letraDisplay"> Servicio</td>
                                    <td colspan="2" class="letraDisplay"><select name="cod_servicio" class="input"
                                            id="cod_servicio" title="Servicio">
                                            <option>[SELECCIONE]</option>
                                            <?php  $model->Mylista("sis_tipo", 5); ?>
                                        </select>
                                        <span class="letra">
                                            <input name="cantidad" type="hidden" class="norm" id="cantidad"
                                                onfocus="select()"
                                                onkeypress="return validFormat(this,event,'Int','nextFocus(\'valor\')')"
                                                value="1" size="10" />
                                        </span></td>
                                </tr>
                                <tr>
                                    <td height="30" class="letraDisplay"> Procedimiento</td>
                                    <td width="255" colspan= "2" class="letra"><input name="nombre" readonly="readonly" type="text"
                                            class="input" id="nombre" size="60" /></td>
                                    
                                </tr>
								 <tr>
                                    <td height="30" class="letraDisplay"> Vlr Unit</td>
                                    <td width="255" class="letra">
									 <input name="valor" type="text" class="input" id="valor" size="20" />	
									 <input name="Botón" type="button" class="input" value="Ingresar"	
                                                onclick="Enviar('f', 'CtrlPaquetes.php?operacion=ing_elem', '|')" />									 
									</td>
                                    <td width="170" class="letra"><span class="letraDisplay">
                                       

                                           
                                        </span></td>
                                </tr>
                            </table>
                        </th>
                    </tr>
                    <tr>
                        <th height="158" scope="col">
                            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"
                                class="LSPRESSE">
                                <tr>
                                    <td colspan="3">
                                        <!-- capa -->
                                        <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
                                            <tr>
                                                <td width="65" height="19" align="center"
                                                    background="../../imagenes/bgtop.gif" class="letraCaptionNegrita">
                                                    <?php echo $i18n->translate('cups'); ?></td>
                                                <td width="536" align="center" background="../../imagenes/bgtop.gif"
                                                    class="letraCaptionNegrita">Nombre Procedimiento </td>
                                            </tr>
                                        </table>
                                        <!-- TABLA DE PORC PARA CIRUGIA -->
                                        <center>
                                            <div id="layPublic"
                                                style="position: relative; left: 0px; top: 0px; width: 100%; height: 250; z-index: 1; visibility: visible; overflow: auto">
                                                <table width="100%" border="0" align="left" cellpadding="0"
                                                    cellspacing="0">
                                                    <?php
                                                    if (isset($_SESSION["procs"]) && $_SESSION["procs"] != NULL) {
                                                    $procs = $_SESSION["procs"];
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <table width="100%" border="0" align="left" cellpadding="1"
                                                                cellspacing="1">
                                                                <tr
                                                                    style="font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size: 13px; ">
                                                                    <td colspan="3" align="left" class="nomGrupo">
                                                                        PROCEDIMIENTOS FORMULADOS</td>
                                                                </tr>
                                                                <?php
                                                                for ($j = 0; $j < sizeof($procs); $j++)
                                                                {
                                                                    $procs[$j]["codigo"];
                                                                $color = $arrBgColor[($i%2)];
                                                                ?>
                                                                <tr bgcolor='<?php echo $color; ?>' class="capa2"
                                                                    style="font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size: 13px; ">
                                                                    <td width="65" height="28" align="left"
                                                                        valign="middle" class="letraDisplay">
                                                                        <?php echo $procs[$j]["codigo"]; ?></td>
                                                                    <td width="510" align="left" valign="middle"
                                                                        class="letraDisplay">
                                                                        &nbsp;<?php echo $procs[$j]["nombre"];  ?></td>
                                                                    <td width="23" align="left" valign="middle"
                                                                        class="letraDisplay"><a
                                                                            href="CtrlPaquetes.php?operacion=del_elem_proc&paquete=<?php echo $_SESSION["paquete"];?>&nombre_Contrato=<?php echo $_SESSION["nombre_Contrato"]?>&codproc=<?php echo $procs[$j]["codigo"];?>&codigo_paquete=<?php echo $_SESSION['codigo_paquete'];?>&idmanual=<?php echo $_SESSION['codigo_precio'];?>&p=<?php echo $j; ?>"><img
                                                                                src="../../imagenes/Eliminar.jpg"
                                                                                border="0" /></a></td>
                                                                </tr>
                                                                <?php
			}
	  ?>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <!-- FIN DE CAPA -->
                                                    <?php
		}

		if (isset($_SESSION["med"]) && $_SESSION["med"] != NULL) {
			$med = $_SESSION["med"];
	  ?>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <!-- FIN DE CAPA -->
                                                    <?php
		}
	 ?>
                                                </table>
                                            </div>
                                        </center>
                                    </td>
                                </tr>
                            </table>
                        </th>
                  </tr>
                </table>
            </td>
        </tr>
    </table>
</form>
<br />
<?php if($_REQUEST['respuesta']==='ok'){ ?>
    <script>
        alert('El Registro Se Guardo Correctamente');
        disableInputs();
    </script>
<?php }if($_REQUEST['respuesta']==='error'){ ?>
        <script>alert('No Es Posible Guardar');</script>
<?php } ?>

<?php if($_REQUEST['getPaq']==='ok'){ ?>
    <script>
        disableInputs();
    </script>
<?php } ?>
