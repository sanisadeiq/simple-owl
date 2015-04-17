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
		return view('treeview');
	}

	public function getInitialTree()
	{
		// Load OWL file in XML format
		$xml = simplexml_load_file("RC - Ontologia - Consumos Alimentares.owl");

		// Initialize arrays
		$arrayClasses = array();
		$arrayObjectProperties = array();
		$arrayDataProperties = array();
		$arrayIndividuals = array();

		// Create array for classes' ID's
		$i = 1;
		$classesIDs = array();
		$classesIDs['Thing'] = 0;
		
		foreach ( $xml->Declaration as $declaration ) {

			foreach ( $declaration->Class as $class ) {

				$classTitle = ltrim ( (string) $class['IRI'], '#' );
				$classesIDs[$classTitle] = $i;
				$i++;
			}
		}

		/*// Create array for individuals' ID's
		$i = 1;
		$individualsIDs = array();

		foreach ($xml->Declaration as $declaration) {

			foreach ($declaration->NamedIndividual as $individual) {

				$individualName = ltrim ( (string) $individual['IRI'], '#' );
				$individualsIDs[$individualName] = $i;
				$i++;
			}
		}*/

		// Handle ontology elements ('Declaration')
		foreach ( $xml->Declaration as $declaration ) {

			// Populate classes' array
			foreach ( $declaration->Class as $class ) {

				$classTitle = ltrim ( (string) $class['IRI'], '#' );

				$arrayClasses[] = array(
					'id' => $classesIDs[$classTitle],
					'title' => $classTitle,
					'parent_id' => 0,
					'active' => false,
					'individuals' => array(),
					'necessaryConditions' => array(),
					'sufficientConditions' => array(),
					'disjointWith' => array()
				);
			}

			// Populate individuals' array
			foreach ( $declaration->NamedIndividual as $individual ) {

				$individualName = ltrim ( (string) $individual['IRI'], '#' );

				$arrayIndividuals[] = array(
/*					'id' => $individualsIDs[$individualName],*/
					'name' => $individualName,
					'objectProperties' => array(),
					'dataProperties' => array(),
					'negativeObjectProperties' => array(),
					'negativeDataProperties' => array(),
					'sameIndividuals' => array(),
					'differentIndividuals' => array()
				);
			}

			// Populate object properties' array
			foreach ( $declaration->ObjectProperty as $objectProperty ) {

				$objectPropertyName = ltrim ( (string) $objectProperty['IRI'], '#' );
				$arrayObjectProperties[] = $objectPropertyName;
			}

			// Populate data properties' array
			foreach ( $declaration->DataProperty as $dataProperty ) {

				$dataPropertyName = ltrim ( (string) $dataProperty['IRI'], '#' );
				$arrayDataProperties[] = $dataPropertyName;
			}

		}

		/* CLASSES */
		// Handle necessary & sufficient conditions ('EquivalentClasses')
		foreach ( $xml->EquivalentClasses as $sufficientCondition ) {

			$classIRI = ltrim ( (string) $sufficientCondition->Class['IRI'], '#' );
			$keyClassToChange = 0;

			// Set ID of class to change
			foreach ( $arrayClasses as $key => $value ) {

				if ( strcmp( $arrayClasses[$key]['title'], $classIRI ) == 0 ) {

					$keyClassToChange = $key;
				}
			}

			// Handle "ObjectHasValue"
			if ( count ( $sufficientCondition->ObjectHasValue ) == 1 ) {

				$objectProperty = ltrim ( (string) $sufficientCondition->ObjectHasValue->ObjectProperty['IRI'], '#' );
				$individual = ltrim ( (string) $sufficientCondition->ObjectHasValue->NamedIndividual['IRI'], '#' );

				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $objectProperty . " value " . $individual;

			}

			// Handle "DataHasValue"
			else if ( count ( $sufficientCondition->DataHasValue ) == 1 ) {

				$dataProperty = ltrim( (string) $sufficientCondition->DataHasValue->DataProperty['IRI'], '#' );
				$literalDatatype = (string) $sufficientCondition->DataHasValue->Literal['datatypeIRI'];

				if ( strpos( $literalDatatype, "PlainLiteral" ) !== true ) {

					$literalValue = (string) $sufficientCondition->DataHasValue->Literal;
					$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $dataProperty . " value " . $literalValue;
				}

				else {

					$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $dataProperty . " some Literal";
				}

				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $literalDatatype;
				
			}

			// Handle "ObjectSomeValuesFrom"
			else if ( count ( $sufficientCondition->ObjectSomeValuesFrom ) == 1 ) {

				$objectProperty = ltrim ( (string) $sufficientCondition->ObjectSomeValuesFrom->ObjectProperty['IRI'], '#' );
				$class = ltrim ( (string) $sufficientCondition->ObjectSomeValuesFrom->Class['IRI'], '#' );

				$arrayClasses[$keyClassToChange]['sufficientConditions'][] =  $objectProperty . " some " . $class;
			}

			// Handle "DataSomeValuesFrom"
			else if ( count ( $sufficientCondition->DataSomeValuesFrom ) == 1 ) {

				$dataProperty = ltrim ( (string) $sufficientCondition->DataSomeValuesFrom->DataProperty['IRI'], '#' );
				$datatype = (string) $sufficientCondition->DataSomeValuesFrom->Datatype['abbreviatedIRI'];
				$datatype = substr( $datatype, strpos( $datatype, ":" ) + 1, strlen( $datatype ) - 1 );

				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $dataProperty . " some " . $datatype;
			}

			// Handle "ObjectAllValuesFrom"
			else if ( count ( $sufficientCondition->ObjectAllValuesFrom ) == 1 ) {

				$objectProperty = ltrim ( (string) $sufficientCondition->ObjectAllValuesFrom->ObjectProperty['IRI'], '#' );
				$class = ltrim ( (string) $sufficientCondition->ObjectAllValuesFrom->Class['IRI'], '#' );

				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $objectProperty . " only " . $class;

			}

			// Handle "DataAllValuesFrom"
			else if ( count ( $sufficientCondition->DataAllValuesFrom ) == 1 ) {

				$dataProperty = ltrim ( (string) $sufficientCondition->DataAllValuesFrom->DataProperty['IRI'], '#' );
				$datatype = (string) $sufficientCondition->DataAllValuesFrom->Datatype['abbreviatedIRI'];
				$datatype = substr( $datatype, strpos( $datatype, ":" ) + 1, strlen( $datatype ) - 1 );

				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $dataProperty . " only " . $datatype;
			}

			// Handle "ObjectUnionOf"
			else if ( count ( $sufficientCondition->ObjectUnionOf ) == 1 ) {

				$sufficientConditionFinal = "";
				
				if ( isset ( $sufficientCondition->ObjectUnionOf->ObjectHasValue ) ) {

					foreach ( $sufficientCondition->ObjectUnionOf->ObjectHasValue as $objectHasValue ) {

						$objPropName = ltrim ( (string) $objectHasValue->ObjectProperty['IRI'], '#' );
						$indPropName = ltrim ( (string) $objectHasValue->children()[1]['IRI'], '#' );
						$sufficientConditionFinal .= "(" . $objPropName . " value " . $indPropName . ") or ";
					}

				} else if ( isset ( $sufficientCondition->ObjectUnionOf->Class ) ) {

					foreach ( $sufficientCondition->ObjectUnionOf->Class as $class ) {

						$className = ltrim ( (string) $class['IRI'], '#' );
						$sufficientConditionFinal .= $className . " or ";
					}
				}

				$sufficientConditionFinal = substr ( $sufficientConditionFinal, 0, -4 );

				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $sufficientConditionFinal;

			}

			// Handle "ObjectIntersectionOf"
			else if ( count ( $sufficientCondition->ObjectIntersectionOf ) == 1 ) {

				$sufficientConditionFinal = "";
				$objectSomeValuesFrom = array();
				$dataSomeValuesFrom = array();
				$objectAllValuesFrom = array();
				$dataAllValuesFrom = array();

				foreach ( $sufficientCondition->ObjectIntersectionOf->children() as $intersection ) {


					if ( isset ( $intersection->ObjectProperty ) && strpos( $intersection->getName(), "Some") !== true) {

						$objectProperty = ltrim ( (string) $intersection->ObjectProperty['IRI'], '#' );
						$class = ltrim ( (string) $intersection->Class['IRI'], '#' );

						$objectSomeValuesFrom[] = "(" . $objectProperty . " some " . $class . ")";
					}

					else if ( isset ( $intersection->DataProperty ) && strpos( $intersection->getName(), "Some") !== true ) {

						$dataProperty = ltrim ( (string) $intersection->DataProperty['IRI'], '#' );
						$datatype = (string) $intersection->Datatype['abbreviatedIRI'];
						$datatype = substr ( $datatype, strpos ( $datatype, ":" ) + 1, strlen ( $datatype ) - 1 );

						$dataSomeValuesFrom[] = "(" . $dataProperty . " some " . $datatype . ")";

					}

					else if ( isset ( $intersection->ObjectProperty ) && strpos( $intersection->getName(), "All") !== true ) {

						$objectProperty = ltrim ( (string) $intersection->ObjectProperty['IRI'], '#' );
						$class = ltrim ( (string) $intersection->Class['IRI'], '#' );

						$objectSomeValuesFrom[] = "(" . $objectProperty . " only " . $class . ")";
					}

					else if ( isset ( $intersection->DataProperty ) && strpos( $intersection->getName(), "All") !== true ) {

						$dataProperty = ltrim ( (string) $intersection->DataProperty['IRI'], '#' );
						$datatype = (string) $intersection->Datatype['abbreviatedIRI'];
						$datatype = substr ( $datatype, strpos ( $datatype, ":" ) + 1, strlen ( $datatype ) - 1 );

						$dataSomeValuesFrom[] = "(" . $dataProperty . " only " . $datatype . ")";
					}
				}

				foreach ( $objectSomeValuesFrom as $conditionElement ) {

					$sufficientConditionFinal .= $conditionElement . " and ";
				}

				foreach ( $dataSomeValuesFrom as $conditionElement ) {

					$sufficientConditionFinal .= $conditionElement . " and ";
				}

				$sufficientConditionFinal = substr ( $sufficientConditionFinal, 0, -5 );

				$arrayClasses[$keyClassToChange]['sufficientConditions'][] = $sufficientConditionFinal;
			}
		}

		// Handle necessary conditions (SubClassOf")
		foreach ($xml->SubClassOf as $subclass) {

			$subclassName = ltrim ( (string) $subclass->Class[0]['IRI'], '#' );

			if ( count ( $subclass->Class ) == 2 ) {

				foreach ( $arrayClasses as $classKey => $classValue ) {

					if ( strcmp ( $arrayClasses[$classKey]['title'], $subclassName ) == 0 ) {

						$parentName = ltrim ( (string) $subclass->Class[1]['IRI'], '#' );

						if ( strcmp ( $parentName, "" ) == 0 ) {

							$parentName = ltrim ( (string) $subclass->Class[1]['abbreviatedIRI'], ':' );
						}

						$arrayClasses[$classKey]['parent_id'] = $classesIDs[$parentName];
					}
				}
			}

			else if ( count ( $subclass->DataHasValue ) == 1 ) {

				foreach ( $arrayClasses as $classKey => $classValue ) {

					if ( strcmp ( $arrayClasses[$classKey]['title'], $subclassName ) == 0 ) {

						$dataProperty = ltrim ( (string) $subclass->DataHasValue->DataProperty['IRI'], '#' );

						$arrayClasses[$classKey]['necessaryConditions'][] = $dataProperty . " value " . (string) $subclass->DataHasValue->Literal;

					}
				}
			}

			/*else if ( count ( $subclass->ObjectIntersectionOf ) == 1 ) {

				foreach ( $arrayClasses as $classKey => $classValue ) {

					if ( strcmp ( $arrayClasses[$classKey]['title'], $subclassName ) == 0 ) {

						foreach ($subclass->ObjectIntersectionOf->Class as $classForIntersection) {
							
							$class = ltrim ( (string) $subclass->ObjectIntersectionOf->Class['IRI'], '#' );

							!!!$arrayClasses[$classKey]['necessaryConditions'][] = $dataProperty . " value " . (string) $subclass->DataHasValue->Literal;

						}
					}
				}

			}*/
			
		}

		// Handle "DisjointClasses"
		foreach ( $xml->DisjointClasses as $classes ) {

			$disjointClasses = array();

			foreach ( $classes->Class as $disjointClass ) {

				$disjointClasses[] = ltrim ( (string) $disjointClass['IRI'], '#' );
			}

			foreach ( $arrayClasses as $key => $value ) {

				foreach ( $disjointClasses as $disjointClass ) {

					if ( strcmp ( $arrayClasses[$key]['title'], $disjointClass ) == 0 ) {

						foreach ( $disjointClasses as $disjointClassExceptSelf ) {

							if ( strcmp ( $disjointClassExceptSelf , $disjointClass ) != 0 ) {

								$arrayClasses[$key]['disjointWith'][] = $disjointClassExceptSelf;
							}
						}
					}
				}
			}
		}

		// Handle individuals in class ("ClassAssertion")
		foreach ( $xml->ClassAssertion as $classIndividual ) {

			$class = ltrim ( (string) $classIndividual->Class['IRI'], '#' );

			foreach ( $arrayClasses as $classKey => $classValue ) {

				if ( strcmp ( $arrayClasses[$classKey]['title'], $class ) == 0 ) {

					$individual = ltrim ( (string) $classIndividual->NamedIndividual['IRI'], '#' );
					$arrayClasses[$classKey]['individuals'][] = $individual;
				}
			}
		}
	
		/* INDIVIDUALS */
		// Handle individuals' object properties ("ObjectPropertyAssertion")
		foreach ( $xml->ObjectPropertyAssertion as $assertion ) {

			$objectProperty = ltrim ( ( string ) $assertion->ObjectProperty['IRI'], '#' );
			$firstIndividual = ltrim ( ( string ) $assertion->NamedIndividual[0]['IRI'], '#' );
			$secondIndividual = ltrim ( ( string ) $assertion->NamedIndividual[1]['IRI'], '#' );

			foreach ($arrayIndividuals as $key => $value) {

				if ( strcmp ( $arrayIndividuals[$key]['name'], $firstIndividual ) == 0 ) {

					$arrayIndividuals[$key]['objectProperties'][] = $firstIndividual . " " . $objectProperty . " " . $secondIndividual;
				}
			}
		}

		// Handle individuals' negative object properties ("NegativeObjectPropertyAssertion")
		foreach ( $xml->NegativeObjectPropertyAssertion as $assertion ) {

			$objectProperty = ltrim ( ( string ) $assertion->ObjectProperty['IRI'], '#' );
			$firstIndividual = ltrim ( ( string ) $assertion->NamedIndividual[0]['IRI'], '#' );
			$secondIndividual = ltrim ( ( string ) $assertion->NamedIndividual[1]['IRI'], '#' );

			foreach ($arrayIndividuals as $key => $value) {

				if ( strcmp ( $arrayIndividuals[$key]['name'], $firstIndividual ) == 0 ) {

					$arrayIndividuals[$key]['negativeObjectProperties'][] = $firstIndividual . " " . $objectProperty . " " . $secondIndividual;
				}
			}
		}

		// Handle individuals' data properties ("DataPropertyAssertion")
		foreach ( $xml->DataPropertyAssertion as $assertion ) {

			$dataProperty = ltrim ( ( string ) $assertion->DataProperty['IRI'], '#' );
			$individual = ltrim ( ( string ) $assertion->NamedIndividual['IRI'], '#' );
			$literalDatatype = ltrim ( ( string ) $assertion->Literal['datatypeIRI'], '#' );
			$literal = "";

			if ( strpos( $literalDatatype, "PlainLiteral" ) == true ) {

				$literal = "Literal";
			}

			else {

				$literal = ( string ) $assertion->Literal;
			}

			foreach ($arrayIndividuals as $key => $value) {

				if ( strcmp ( $arrayIndividuals[$key]['name'], $individual ) == 0 ) {

					$arrayIndividuals[$key]['dataProperties'][] = $dataProperty . " " . $literal;
				}
			}
		}

		// Handle individuals' negative data properties ("NegativeDataPropertyAssertion")
		foreach ( $xml->NegativeDataPropertyAssertion as $assertion ) {

			$dataProperty = ltrim ( ( string ) $assertion->DataProperty['IRI'], '#' );
			$individual = ltrim ( ( string ) $assertion->NamedIndividual['IRI'], '#' );
			$literalDatatype = ltrim ( ( string ) $assertion->Literal['datatypeIRI'], '#' );
			$literal = "";

			if ( strpos( $literalDatatype, "PlainLiteral" ) == true ) {

				$literal = "Literal";
			}

			else {

				$literal = ( string ) $assertion->Literal;
			}

			foreach ($arrayIndividuals as $key => $value) {

				if ( strcmp ( $arrayIndividuals[$key]['name'], $individual ) == 0 ) {

					$arrayIndividuals[$key]['negativeDataProperties'][] = $dataProperty . " " . $literal;
				}
			}
		}

		// Handle same individuals ("SameIndividual")
		// TODO: será que pode haver mais do que dois indivíduos dentro desta tag? Se sim, fazer como em DisjointClasses
		foreach ( $xml->SameIndividual as $individualsPair ) {

			$firstIndividual = ltrim ( ( string ) $individualsPair->NamedIndividual[0]['IRI'], '#' );
			$secondIndividual = ltrim ( ( string ) $individualsPair->NamedIndividual[1]['IRI'], '#' );

			foreach ($arrayIndividuals as $key => $value) {

				if ( strcmp ( $arrayIndividuals[$key]['name'], $firstIndividual ) == 0 ) {

					$arrayIndividuals[$key]['sameIndividuals'][] = $secondIndividual;
				}

				else if ( strcmp ( $arrayIndividuals[$key]['name'], $secondIndividual ) == 0 ) {

					$arrayIndividuals[$key]['sameIndividuals'][] = $firstIndividual;
				}
			}
		}

		// Handle different individuals ("DifferentIndividuals")
		// TODO: será que pode haver mais do que dois indivíduos dentro desta tag? Se sim, fazer como em DisjointClasses
		foreach ( $xml->DifferentIndividuals as $individualsPair ) {

			$firstIndividual = ltrim ( ( string ) $individualsPair->NamedIndividual[0]['IRI'], '#' );
			$secondIndividual = ltrim ( ( string ) $individualsPair->NamedIndividual[1]['IRI'], '#' );

			foreach ($arrayIndividuals as $key => $value) {

				if ( strcmp ( $arrayIndividuals[$key]['name'], $firstIndividual ) == 0 ) {

					$arrayIndividuals[$key]['differentIndividuals'][] = $secondIndividual;
				}

				else if ( strcmp ( $arrayIndividuals[$key]['name'], $secondIndividual ) == 0 ) {

					$arrayIndividuals[$key]['differentIndividuals'][] = $firstIndividual;
				}
			}
		}


		/* TREE */
		// Prepare TreeView
		foreach ( $arrayClasses as $class ) {

			// Display only top classes
			if ( $class['parent_id'] == 0 ) {

				$class['active'] = true;
			}

			$class['subs'] = array();
			$indexedItems[$class['id']] = (object) $class;
		}

		$arrayTree = array();

		foreach ( $indexedItems as $item ) {

			if ( $item->parent_id == 0 ) {

				$arrayTree[] = $item;
			}

			else {

				$indexedItems[$item->parent_id]->subs[] = $item;
			}
		}

		$finalArray = array( "tree" => $arrayTree, 
							 "classes" => $arrayClasses, 
							 "objectProperties" => $arrayObjectProperties, 
							 "dataProperties" => $arrayDataProperties, 
							 "individuals" => $arrayIndividuals );
		
		return $finalArray;
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