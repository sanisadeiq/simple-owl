<?php namespace App\Http\Controllers;

class TreeViewController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| TreeView Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the XML parsing and sends the result to the page.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// I believe this restricts the access only to guests, therefore it's commented.
		//$this->middleware('guest');
	}

	/**
	 * Parse the XML.
	 *
	 * @return Response
	 */
	public function index()
	{

		// foreach ($arrayClasses as $class) {
		// 	echo $class['id'] . " -> " . $class['title'] . " subclass of: " . $class['parent_id'] . "</br>"; 
		// } 

		//$xml = simplexml_load_file("example.xml");

		//$result = $xml->xpath("/*/*/class");

		//print_r($result);
		return view('treeview');
	}

	public function getInitialTree()
	{
		$xml = simplexml_load_file("RC - Ontologia - Consumos Alimentares.owl");
		$arrayClasses = array();
		$arrayObjProp = array();
		$arrayDataProp = array();
		$arrayIndividuals = array();

		$i = 1;
		$ids = array();
		$ids['Thing'] = 0;
		//$items = array();

		foreach ($xml->Declaration as $decl) {
			foreach ($decl->Class as $class) {
				//echo $class['IRI']."</br>";
				$classTitle = ltrim ((string)$class['IRI'], '#');
				$ids[$classTitle] = $i;
				$i++;
			}
		}

		foreach ($xml->Declaration as $decl) {
			foreach ($decl->Class as $class) {
				$classTitle = ltrim ((string)$class['IRI'], '#');	
				// echo "class ->" . $classTitle . "</br>";
				$arrayClasses[] = array(
					'id' => $ids[$classTitle],
					'title' => $classTitle,
					'parent_id' => 0,
					'active' => true,
					'individuals' => array(),
					'necessaryConditions' => array(),
					'disjointWith' => array(),
					'sufficientConditions' => array()
				);
			}
			foreach ($decl->ObjectProperty as $objProp) {
				$objPropName = ltrim ((string)$objProp['IRI'], '#');
				$arrayObjProp[] = $objPropName;
				// echo "object Property ->" . $objPropName . "</br>";
			}
			foreach ($decl->DataProperty as $dataProp) {
				$dataPropName = ltrim ((string)$dataProp['IRI'], '#');
				$arrayDataProp[] = $dataPropName;
				// echo "data Property ->" . $dataPropName . "</br>";
			}
			foreach ($decl->NamedIndividual as $individual) {
				$individualName = ltrim ((string)$individual['IRI'], '#');
				$arrayIndividuals[] = $individualName;
				// echo "individual ->" . $individualName . "</br>";
			}
		}

		foreach ($xml->SubClassOf as $classes) {
			$subclassIRI = ltrim ((string)$classes->Class[0]['IRI'], '#');
			if(count($classes->Class) == 2){
				// print_r($subclassIRI);
				// echo "</br>";

				foreach ($arrayClasses as $classKey => $classValue) {
					if(strcmp($arrayClasses[$classKey]['title'], $subclassIRI) == 0){

						$parentIRI = ltrim ((string)$classes->Class[1]['IRI'], '#');
						$arrayClasses[$classKey]['parent_id'] = $ids[$parentIRI];
						// echo "Parent ID of " . $class['title'] . " = " . $ids[$parentIRI] . "</br>";
					}
				}
			}else if(count($classes->DataHasValue) == 1){
				foreach ($arrayClasses as $classKey => $classValue) {
					if(strcmp($arrayClasses[$classKey]['title'], $subclassIRI) == 0){

						$dataPropName = ltrim ((string)$classes->DataHasValue->DataProperty['IRI'], '#');
						$arrayClasses[$classKey]['necessaryConditions'][] = $dataPropName . " value " .(string)$classes->DataHasValue->Literal;
						// echo "Parent ID of " . $class['title'] . " = " . $ids[$parentIRI] . "</br>";
					}
				}
			}
			
		}
		
		foreach ($xml->ClassAssertion as $classInd) {
			$classIRI = ltrim ((string)$classInd->Class['IRI'], '#');
			foreach ($arrayClasses as $classKey => $classValue) {
				if(strcmp($arrayClasses[$classKey]['title'], $classIRI) == 0){
					$indIRI = ltrim ((string)$classInd->NamedIndividual['IRI'], '#');
					$arrayClasses[$classKey]['individuals'][] = $indIRI;
				}
			}
		}

		foreach ($xml->DisjointClasses as $classes) {
			$classIRI1 = ltrim ((string)$classes->Class[0]['IRI'], '#');
			$classIRI2 = ltrim ((string)$classes->Class[1]['IRI'], '#');
			foreach ($arrayClasses as $key => $value) {
				if(strcmp($arrayClasses[$key]['title'], $classIRI1) == 0){
					$arrayClasses[$key]['disjointWith'][] = $classIRI2;
				}else if(strcmp($arrayClasses[$key]['title'], $classIRI2) == 0){
					$arrayClasses[$key]['disjointWith'][] = $classIRI1;
				}
			}
		}

		foreach ($xml->EquivalentClasses as $sufCond) {
			$classIRI = ltrim ((string)$sufCond->Class['IRI'], '#');
			$keyClassToChange;
			foreach ($arrayClasses as $key => $value) {
					if(strcmp($arrayClasses[$key]['title'], $classIRI) == 0){
						$keyClassToChange = $key;
					}
				}
			if(count($sufCond->DataHasValue) == 1){
				$dataPropName = ltrim ((string)$sufCond->DataHasValue->DataProperty['IRI'], '#');
				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $dataPropName . " some " .(string)$sufCond->DataHasValue->Literal;
				// foreach ($arrayClasses as $key => $value) {
				// 	if(strcmp($arrayClasses[$key]['title'], $classIRI) == 0){
				// 		$dataPropName = ltrim ((string)$sufCond->DataHasValue->DataProperty['IRI'], '#');
				// 		$arrayClasses[$key]['sufficientConditions'][] = $dataPropName . " some " .(string)$sufCond->DataHasValue->Literal;
				// 		// echo "Parent ID of " . $class['title'] . " = " . $ids[$parentIRI] . "</br>";
				// 	}
				// }
			}else if(count($sufCond->ObjectHasValue) == 1){
				$objPropName = ltrim ((string)$sufCond->ObjectHasValue->ObjectProperty['IRI'], '#');
				$indPropName = ltrim ((string)$sufCond->ObjectHasValue->NamedIndividual['IRI'], '#');
				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $objPropName . " value " . $indPropName;
				// foreach ($arrayClasses as $key => $value) {
				// 	if(strcmp($arrayClasses[$key]['title'], $classIRI) == 0){
				// 		$objPropName = ltrim ((string)$sufCond->ObjectHasValue->ObjectProperty['IRI'], '#');
				// 		$indPropName = ltrim ((string)$sufCond->ObjectHasValue->NamedIndividual['IRI'], '#');
				// 		$arrayClasses[$key]['sufficientConditions'][] = $objPropName . " value " . $indPropName;
				// 		// echo "Parent ID of " . $class['title'] . " = " . $ids[$parentIRI] . "</br>";
				// 	}
				// }	
			}else if(count($sufCond->ObjectUnionOf) == 1){
				$sufficientConditionFinal = "";
				foreach ($sufCond->ObjectUnionOf->ObjectHasValue as $objHasValue) {
					$objPropName = ltrim ((string)$objHasValue->ObjectProperty['IRI'], '#');
					$indPropName = ltrim ((string)$objHasValue->children()[1]['IRI'], '#');
					$sufficientConditionFinal .= "(" . $objPropName . " value " . $indPropName . ")" . " or ";
				}
				$sufficientConditionFinal = substr($sufficientConditionFinal, 0, -4);
				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $sufficientConditionFinal;
			}else if(count($sufCond->ObjectIntersectionOf) == 1){
				// $sufficientConditionFinal = "";
				// foreach ($sufCond->ObjectIntersectionOf->ObjectSomeValuesFrom as $objSomeValFrom) {
				// 	$objPropName = ltrim ((string)$objSomeValFrom->ObjectProperty['IRI'], '#');
				// 	$indPropName = ltrim ((string)$objSomeValFrom->NamedIndividual['IRI'], '#');
				// 	$sufficientConditionFinal .= "(" . $objPropName . " value " . $indPropName . ")" . " or ";
				// }
				// $sufficientConditionFinal = substr($sufficientConditionFinal, 0, -4);
				// $arrayClasses[$keyClassToChange]['sufficientConditions'][] = $sufficientConditionFinal;
			}
		}



		//PREPARE TREE
		foreach ($arrayClasses as $item) {
			$item['subs'] = array();
			$indexedItems[$item['id']] = (object) $item;
		}

		// assign to parent
		$topLevel = array();

		foreach ($indexedItems as $item) {
			if ($item->parent_id == 0) {
				$topLevel[] = $item;
			} else {
				$indexedItems[$item->parent_id]->subs[] = $item;
			}
		}

		$final = array( "tree" => $topLevel, "classes" => $arrayClasses, "objProp" => $arrayObjProp, "dataProp" => $arrayDataProp, "individual" => $arrayIndividuals);
		return $final;
	}
		// $cmd = "java -jar phpOut.jar";
		// $outputfile = "OUTPUT";
		// $pidArr = array();
		// exec(sprintf("%s > %s 2>&1 & echo $!", $cmd, $outputfile),$pidArr);

		// function isRunning($pid){
		// 	try{
		// 		$result = shell_exec(sprintf("ps %d", $pid));
		// 		if( count(preg_split("/\n/", $result)) > 2){
		// 			return true;
		// 		}
		// 	}catch(Exception $e){}

		// 	return false;
		// }

		// while(isRunning($pidArr[0])){
		// 	sleep(1);
		// }



		// $xml = simplexml_load_file("example.xml") or die("Error: Cannot create object");

		// $i = 1;
		// $ids = array();
		// $ids['Thing'] = 0;
		// $items = array();

		// foreach ($xml->class as $class) {
		// 	//print_r($class['name'.""]. '<br>');
		// 	$ids[$class['name'].""] = $i;
		// 	$i++;
		// }

		// foreach ($xml->class as $class) {
		// 	//echo sprintf('parent: %s', $class['parent']) . "<br>";
		// 	array_push($items, array(
		// 		'id' => $ids[$class['name'].""],
		// 		'title' => $class['name'] . "",
		// 		'parent_id' => $ids[$class['parent'].""],
		// 		'active' => true,
		// 		'type' => 'class'
		// 	));

		// 	foreach ($class->individual as $ind) {
		// 		//echo sprintf('CLASS: %s -> INDIVIDUAL: %s', $class->name, $ind->name) . "<br>";
		// 		array_push($items, array(
		// 			'id' => $i,
		// 			'title' => $ind['name'] . "",
		// 			'parent_id' => $ids[$class['name'].""],
		// 			'active' => true,
		// 			'type' => 'individual'
		// 		));

		// 		$lastInd = $i;
		// 		$i++;

		// 		foreach ($ind->relationship as $rel) {
		// 			//echo sprintf('CLASS: %s -> INDIVIDUAL: %s -> RELATIONSHIP: %s',
		// 			 //$class->name, $ind->name, $rel->name) . "<br>";
		// 			array_push($items, array(
		// 				'id' => $i,
		// 				'title' => $rel['name'] . " " . $rel,
		// 				'parent_id' => $lastInd,
		// 				'active' => true,
		// 				'type' => 'relationship'
		// 			));

		// 			$i++;
		// 		}
		// 	}

		// 	foreach ($class->necessaryCondition as $nec) {
		// 		//echo sprintf('CLASS: %s -> NECCONDITION: %s', $class->name, $nec->condition) . "<br>";
		// 		array_push($items, array(
		// 			'id' => $i,
		// 			'title' => $nec['name'] . "",
		// 			'parent_id' => $ids[sprintf('%s', $class['name'])],
		// 			'active' => true,
		// 			'type' => 'necessaryCondition'
		// 		));

		// 		$i++;
		// 	}

		// 	foreach ($class->sufficientCondition as $suf) {
		// 		//echo sprintf('CLASS: %s -> SUFCONDITION: %s', $class->name, $suf->condition) . "<br>";
		// 		array_push($items, array(
		// 			'id' => $i,
		// 			'title' => $suf['name'] . "",
		// 			'parent_id' => $ids[sprintf('%s', $class['name'])],
		// 			'active' => true,
		// 			'type' => 'sufficientCondition'
		// 		));

		// 		$i++;
		// 	}
		// }

		// foreach ($items as $item) {
		// 	$item['subs'] = array();
		// 	$indexedItems[$item['id']] = (object) $item;
		// }

		// // assign to parent
		// $topLevel = array();

		// foreach ($indexedItems as $item) {
		// 	if ($item->parent_id == 0) {
		// 		$topLevel[] = $item;
		// 	} else {
		// 		$indexedItems[$item->parent_id]->subs[] = $item;
		// 	}
		// }

		// return $topLevel;	
}