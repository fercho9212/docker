<?php
session_start();
include("../General.php");
include("../../model/Model.php"); // modelo
include("../../model/sismaweb.php"); // modelo

$model = new Model(); // instancia al modelo

$next = "Paquetes.php";
//print_r($_REQUEST);

$_SESSION["codigo_paquete"] 	= empty($codigo_paquete)  ? $model->getDato("valor", "consecutivo", "tabla = 'codigo_paquete'") : $codigo_paquete;

$_SESSION['paquete'] 			= empty($paquete)		  ?  ''   : $paquete;
$_SESSION['cargo'] 			    = empty($cargo) 		  ?  ''   : $cargo;

$_SESSION['tipo_evaluacion']	= empty($tipo_evaluacion) ?  ''    : $tipo_evaluacion;


$_SESSION['codigo_empresa'] 	= empty($codigo_empresa)  ?  ''    : $codigo_empresa;
$_SESSION['nombre_empresa']		= empty($nombre_empresa)  ?  ''    : $nombre_empresa;

$_SESSION['codigo_precio'] 		= empty($codigo_precio)  ?  ''    : $codigo_precio;
$_SESSION['nombre_precio']		= empty($nombre_precio)  ?  ''    : $nombre_precio;

$_SESSION['idcategoria']		= empty($idcategoria)     ?  ''    : $idcategoria;
$_SESSION['categoria']			= empty($categoria)       ?  ''    : $categoria;

$_SESSION["nombre_Contrato"]    = empty($nombre_Contrato)  ?  ''    : $nombre_Contrato;
$_SESSION["codigo_contrato"]    = empty($codigo_contrato)  ?  ''    : $codigo_contrato;
$tiposEnfasis = implode("&&", $tipo_enfasis);
$_SESSION['tipo_enfasis']		= empty($tiposEnfasis) ?  ''  : $tiposEnfasis;

switch ($operacion) {
	case 'del_proc':
		del_proc();
	break;
	case 'buscar_paq':
	
		buscar_paq();
	break;
	case 'ing_elem':
		ing_elem();
	break;
	case 'eliminar_paq'	:	
		eliminar_paq();
		nuevo_paq();
	

	break;
	case 'del_elem_proc':

		del_elem_proc();
		abrir_paq();
		
	break;
	case 'abrir_paq':

		abrir_paq();
	 
	break;	
	case'recalcular':

	 	global $model,$valor_paquete,$next;		
		recalcular($model, $valor_paquete);
		$next .= "?operacion=mos_paq";
	break;
	case "nuevo_paq":
	nuevo_paq();
		break;	

	case 'guardar_paq' :
		$next .= "?operacion=mos_paq";
	break;
	default:
		clearSessionObject ();
		$next .= "?operacion=mos_paq";
		break;

}


header("Location: $next");

function  nuevo_paq(){
	global $next;
	clearSession();
		$next .= "?operacion=mos_paq";
}
function buscar_paq(){

	global $next;
	$next .= "?operacion=busc_paq";

}
function ing_elem (){
	
	$respuesta = '';
	   $model = new Model();
	
	global $nombre,$codigo,$tipo_elemento,$tiposervicio,$cod_servicio,$valor, $next,$enfasis ;
	$codigo_precio=$_SESSION['codigo_precio']; 
	$nom_usuario=$_SESSION['usuario']; 
	$codigo_usuario=$_SESSION['codigo_user'];
	$actualizacion=date("Y-m-d H:i:s");
	$codigo_paquete=$_SESSION["codigo_paquete"];
        $codigo_contrato=$_SESSION["codigo_contrato"];
	$paquete=$_SESSION["paquete"];
	if ($_SESSION["codigo_paquete"] != NULL && $_SESSION["codigo_paquete"] != "") {
		$next .= "?operacion=mos_paq";
			
		// Averigua si ya existen registros de este paquete almacenados
		$rs = $model->select("COUNT(*)", "paquete", "codigopaq = {$_SESSION['codigo_paquete']}");
		$row = $model->nextRow($rs);
		$model->freeResult($rs);
		// Coloca la tabla de consecutivos al proximo numero identificador disponible
		if ($row[0] == 0) {
			$model->establecerSiguiente("codigo_paquete");
		}

		$numero = $model->obtenerSiguiente("id_paquete");

		$_SESSION['tipo_enfasis']=$enfasis  ; //$enfasis -> hace referencia al campo hidden enfasis 
		if (!(isset($_SESSION["procs"]) && $_SESSION["procs"] != NULL)) {
			$_SESSION["procs"] = array();
		}
		$procs = $_SESSION["procs"];

		$k = sizeof($procs);
		$procs[$k]["id"] 				= $numero;
		$_SESSION["paquete"]			= $paquete;
		$procs[$k]["codigo"] 			= $codigo;
		$procs[$k]["nombre"] 			= $nombre . " - $" . $valor;
		$procs[$k]["servicio"] 		 	= $tipo_elemento == 1 ? $tiposervicio : "-1";
		$procs[$k]["cod_servicio"] 		= $cod_servicio;
		$procs[$k]["valor"] 			= $valor;

		$_SESSION["valor_paquete"]    += $valor;

		try {

			$sql = "INSERT INTO paquete
				(codigopaq, nompaquete, codcirugia, cantidad, codproc, servicio, valor, manual, nomproc,
				cod_servicio, costo, tipo_evaluacion, tipo_enfasis, cargo, codigo_empresa,idcategoria,codigo_usuario,nom_usuario,actualizacion,contrato)
				VALUES
				('{$_SESSION['codigo_paquete']}','{$_SESSION['paquete']}',
				NULL, 1, '$codigo', '$tiposervicio', 0, $codigo_precio,'$nombre',
				'$cod_servicio', '{$_SESSION['costo_paquete']}',NULL,
     				'{$_SESSION['tipo_enfasis']}',NULL,'{$_SESSION['codigo_empresa']}','{$_SESSION['categoria']}','$codigo_usuario','$nom_usuario','$actualizacion','$codigo_contrato')";
                     
		//	echo $sismaweb->debug(true);
			$model->insertar($sql);
			
			$sismaweb 			= new sismaweb();
			
			$valores 			= $sismaweb->cargarTabla("valores");

			$valores->codigo 			= $codigo;
			$valores->tipo				= 'SOAT';
			$valores->idmanual			= $codigo_precio;
			$valores->idpaquete			= $codigo_paquete;
			$valores->valor				= $valor;
			
			$valores->codusuario 		= $_SESSION['codigo_user'];
			$valores->nomusuario 		= $_SESSION['user'];
			$valores->actualizacion 	= date("Y-m-d H:i:s");
			$valores->replace();
			//echo $sismaweb->debug(true)
		
			$_SESSION["procs"] = $procs;
			$respuesta = 'ok';
		} catch (Exception $error) {
			$respuesta = 'error';
		}

		$model->establecerSiguiente("id_paquete");
	}

	$next .= "?operacion=mos_paq&respuesta=" . $respuesta;

}
function del_proc(){
	$cirugias = $_SESSION["cirugias"];
	global $model,$c,$p,$next;          
	$id = $cirugias[$c]['procs'][$p]['id'];
	$sql = "DELETE FROM paquete WHERE id = $id";
	$model->insertar($sql);
	//echo $model->getQuery();

	$nuevo = array();

	for ($i = 0, $k = 0; $i < sizeof($cirugias[$c]['procs']); $i++) {
		if ($i != $p) {
			$nuevo[$k++] = $cirugias[$c]['procs'][$i];
		} else {
			$_SESSION["valor_paquete"] -= $cirugias[$c]['procs'][$i]["valor"];
		}
	}

	if (sizeof($nuevo) == 0) {
		$nuevoc = array();

		for ($i = 0, $k = 0; $i < sizeof($cirugias); $i++) {
			if ($i != $c) {
				$nuevoc[$k++] = $cirugias[$i];
			}
		}

		$_SESSION["cirugias"] = $nuevoc;
	} else {
		$cirugias[$c]['procs'] = $nuevo;
		$_SESSION["cirugias"] = $cirugias;
	}

	$next .= "?operacion=mos_paq";
	
}
function abrir_paq()  {
	global $next,$paquete,$codigo_paquete,$nombre_Contrato;
	$_SESSION["nombre_Contrato"]=$nombre_Contrato;
	$_SESSION["codigo_paquete"]=$codigo_paquete;
	$_SESSION["paquete"]=$paquete;
	$model = new Model();
/* 	print_r($paquete); 
	
	exit();  */
	if (($_SESSION["codigo_paquete"] != NULL && $_SESSION['paquete'] != NULL) && ($_SESSION["codigo_paquete"] != "" && $_SESSION['paquete']!= "")) {
		
		
		
		$rs = $model->select(
		" v.idpaquete,v.codigo,pro.nombreve,v.valor,p.tipo_enfasis,p.idcategoria as categoria,e.codigo AS codigo_empresa ,v.idmanual AS codigo_precio",
		" valores AS v , sis_proc AS pro,paquete AS p ,categoria_cargo as ca , sis_empre AS e",
		"  p.codigopaq=v.idpaquete AND p.codproc=pro.codigo AND p.codproc=v.codigo AND e.codigo=p.codigo_empresa AND p.idcategoria=ca.idcategoria AND v.idpaquete =$codigo_paquete 
		GROUP by v.idpaquete,v.codigo,pro.nombreve,v.valor,p.tipo_enfasis,p.idcategoria,e.codigo,v.idmanual"
		);	
		
       //echo $model->getQuery();
       // exit;
		$procs = array();
		$i = 0;
		$_SESSION["valor_paquete"] =0;

		while (($row = $model->nextRow($rs))) {
			$procs[$i]["id"] 					= $row[0];
			$procs[$i]["codigo"] 				= $row[1];
			$procs[$i]["nombre"] 				= $row[2] . " - $ " .number_format($row[3], 0, '.', '.');
			$procs[$i]["servicio"] 				= $row[4];
			$procs[$i]["valor"] 				= number_format($row[3], 0, '.', '.');
			$procs[$i]["cod_servicio"] 		    = $row[5];

			$i++;
			$_SESSION["valor_paquete"] 	       += $row[3]; 

			$_SESSION["cargo"] 				    = $row['cargo'];
			$_SESSION["tipo_evaluacion"] 		= $row['tipo_evaluacion'];
			$_SESSION["tipo_enfasis"] 			= $row['tipo_enfasis'];
			$_SESSION['nombre_empresa']	  		= $row['nombre_empresa'];
			$_SESSION['categoria']	  			= $row['categoria'];
			$_SESSION['codigo_empresa']	  		= $row['codigo_empresa'];
			$_SESSION['codigo_precio']	  		= $row['codigo_precio'];
		 
		}
		
		$model->freeResult($rs);

		$_SESSION["procs"] = $procs;
		
		if (!($_SESSION["procs"])) {
			$paquete = 'ok';
		}
	}
/*    print_r($_SESSION);
  exit();  */
	$next .= "?operacion=mos_paq&getPaq=ok";
}
function recalcular($model, $valor){
	$valor_paquete = $_SESSION["valor_paquete"];
	$cirugias = $_SESSION["cirugias"];
	$procs = $_SESSION["procs"];
	$med = $_SESSION["med"];
	$total = 0;
	for ($i = 0; $i < sizeof($cirugias); $i++) {
		$cirugias[$i]["valor"] = 0;
		for ($j = 0; $j < sizeof($cirugias[$i]["procs"]); $j++) {
			$cirugias[$i]["procs"][$j]["valor"] = round($valor * ($cirugias[$i]["procs"][$j]["valor"] / $valor_paquete));
			$cirugias[$i]["valor"] += $cirugias[$i]["procs"][$j]["valor"];
			$total += $cirugias[$i]["procs"][$j]["valor"];
			$model->insertar("UPDATE paquete SET valor = {$cirugias[$i]['procs'][$j]['valor']} WHERE id = {$cirugias[$i]['procs'][$j]['id']}");
		}
	}

	$_SESSION["total_procs"] = 0;
	for ($i = 0; $i < sizeof($procs); $i++) {
		$procs[$i]["valor"] = round($valor * ($procs[$i]["valor"] / $valor_paquete));
		$total += $procs[$i]["valor"];
		$_SESSION["total_procs"] += $procs[$i]["valor"];
		$model->insertar("UPDATE paquete SET valor = {$procs[$i]['valor']} WHERE id = {$procs[$i]['id']}");
	}

	$_SESSION["total_med"] = 0;
	for ($i = 0; $i < sizeof($med); $i++) {
		$med[$i]["valor"] = round($valor * ($med[$i]["valor"] / $valor_paquete));
		$total += $med[$i]["valor"];
		$_SESSION["total_med"] += $med[$i]["valor"];
		$model->insertar("UPDATE paquete SET valor = {$med[$i]['valor']} WHERE id = {$med[$i]['id']}");
	}

	$residuo = $valor - $total;

	if (sizeof($cirugias) != 0) {
		for ($i = 0; $i < sizeof($cirugias); $i++) {
			for ($j = 0; $j < sizeof($cirugias[$i]["procs"]); $j++) {
				$cirugias[$i]["procs"][$j]["valor"] += $residuo;
				$cirugias[$i]["valor"] += $residuo;
				$model->insertar("UPDATE paquete SET valor = {$cirugias[$i]['procs'][$j]['valor']} WHERE id = {$cirugias[$i]['procs'][$j]['id']}");
				//echo $model->getQuery();
				break 2;
			}
		}
	} else if (sizeof($procs) != 0) {
		for ($i = 0; $i < sizeof($procs); $i++) {
			$procs[$i]["valor"] += $residuo;
			$_SESSION["total_procs"] += $residuo;
			$model->insertar("UPDATE paquete SET valor = {$procs[$i]['valor']} WHERE id = {$procs[$i]['id']}");
			//echo $model->getQuery();
			break;
		}
	} else if (sizeof($med) != 0) {
		for ($i = 0; $i < sizeof($med); $i++) {
			$med[$i]["valor"] += $residuo;
			$_SESSION["total_med"] += $residuo;
			$model->insertar("UPDATE paquete SET valor = {$med[$i]['valor']} WHERE id = {$med[$i]['id']}");
			//echo $model->getQuery();
			break;
		}
	}

	$_SESSION["valor_paquete"] = $valor;
	$_SESSION["cirugias"] = $cirugias;
	$_SESSION["procs"] = $procs;
	$_SESSION["med"] = $med;
}
function del_elem_proc(){
	$model = new Model();
	$procs =$_SESSION['procs'] ;
	global $codproc,$idmanual,$paquete;	
	$codigo_paquete=$_SESSION["codigo_paquete"];
	

	$sql = "DELETE FROM paquete  WHERE codigopaq='$codigo_paquete' AND codproc = '$codproc'";
	$model->Execute($sql);

	$sql = "DELETE FROM valores  WHERE idpaquete='$codigo_paquete' AND idmanual = '$idmanual' AND codigo = '$codproc'";
	$model->Execute($sql);
	
	$_SESSION["paquete"]=$paquete;
	$_SESSION["codigo_paquete"]=$codigo_paquete;

	
}
function eliminar_paq(){
	global $codproc,$idmanual,$codigo_paquete;	
	$model = new Model();
	$codigo_paquete=$_SESSION["codigo_paquete"];
	$sql = "DELETE FROM paquete WHERE codigopaq = $codigo_paquete";
	$model->Execute($sql);
	$sql = "DELETE FROM valores WHERE idmanual=$idmanual AND idpaquete = $codigo_paquete";
	$model->Execute($sql);
	
}
function existeCirugia($cirugias, $codigo)
{
	for ($i = 0; $i < sizeof($cirugias); $i++) {
		if ($cirugias[$i]["codigo"] == $codigo) {
			return $i;
		}
	}

	return -1;
}

function clearSession()
{
	unset($_SESSION['paquete']);
	unset($_SESSION["codigo_paquete"]);
	unset($_SESSION['tipo_evaluacion']);
	unset($_SESSION['categoria']);
	unset($_SESSION['cargo']);
	unset($_SESSION['idcategoria']);
	unset($_SESSION["procs"]);
	unset($_SESSION['codigo_empresa']);
	unset($_SESSION['codigo_precio']);
	unset($_SESSION["valor_paquete"]);
	unset($_SESSION["nombre_Contrato"]);
	unset($_SESSION['tipo_enfasis']);
}
?>

