<?php namespace App\Http\Controllers;

class TreeViewController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{

		$xml = simplexml_load_file("example.xml") or die("Error: Cannot create object");

		$i = 1;
		$ids = array();
		$ids['Thing'] = 0;
		$results = array();

		foreach ($xml->class as $class) {
			$ids[sprintf('%s', $class->name)] = $i;
			$i++;
		}

		foreach ($xml->children() as $class) {
			//echo sprintf('CLASS: %s', $class->name) . "<br>";
			array_push($results, Array(
				'id' => $ids[sprintf('%s', $class->name)],
				'title' => $class->name . "",
				'parent_id' => $ids[sprintf('%s', $class->parentClass)]
				));
			foreach ($class->individuals as $inds) {
				foreach ($inds->children() as $ind) {
					//echo sprintf('CLASS: %s -> INDIVIDUAL: %s', $class->name, $ind->name) . "<br>";
					array_push($results, Array(
						'id' => $i,
						'title' => $ind->name . "",
						'parent_id' => $ids[sprintf('%s', $class->name)]
						));
					$lastInd = $i;
					$i++;
					foreach ($ind->relationships as $rels) {
						foreach ($rels->children() as $rel) {
							//echo sprintf('CLASS: %s -> INDIVIDUAL: %s -> RELATIONSHIP: %s',
							 //$class->name, $ind->name, $rel->name) . "<br>";
							array_push($results, Array(
								'id' => $i,
								'title' => $rel->name . "",
								'parent_id' => $lastInd
								));
							$i++;
						}
					}
				}
			}
			foreach ($class->necessaryConditions as $necs) {
				foreach ($necs->children() as $nec) {
					//echo sprintf('CLASS: %s -> NECCONDITION: %s', $class->name, $nec->condition) . "<br>";
					array_push($results, Array(
						'id' => $i,
						'title' => $nec->condition . "",
						'parent_id' => $ids[sprintf('%s', $class->name)]
						));
					$i++;
				}
			}
			foreach ($class->sufficientConditions as $sufs) {
				foreach ($sufs->children() as $suf) {
					//echo sprintf('CLASS: %s -> SUFCONDITION: %s', $class->name, $suf->condition) . "<br>";
					array_push($results, Array(
						'id' => $i,
						'title' => $suf->condition . "",
						'parent_id' => $ids[sprintf('%s', $class->name)]
						));
					$i++;
				}
			}
		}

		return view('treeview', ['items' => $results]);
	}

	public function getInitialTree()
	{
		$cmd = "java -jar phpOut.jar";
		$outputfile = "OUTPUT";
		$pidArr = array();
		exec(sprintf("%s > %s 2>&1 & echo $!", $cmd, $outputfile),$pidArr);

		function isRunning($pid){
			try{
				$result = shell_exec(sprintf("ps %d", $pid));
				if( count(preg_split("/\n/", $result)) > 2){
					return true;
				}
			}catch(Exception $e){}

			return false;
		}

		while(isRunning($pidArr[0])){
			sleep(1);
		}

		$xml = simplexml_load_file("example.xml") or die("Error: Cannot create object");

		$i = 1;
		$ids = array();
		$ids['Thing'] = 0;
		$results = array();

		foreach ($xml->class as $class) {
			$ids[sprintf('%s', $class->name)] = $i;
			$i++;
		}

		foreach ($xml->children() as $class) {
			//echo sprintf('CLASS: %s', $class->name) . "<br>";
			array_push($results, Array(
				'id' => $ids[sprintf('%s', $class->name)],
				'title' => $class->name . "",
				'parent_id' => $ids[sprintf('%s', $class->parentClass)]
				));
			foreach ($class->individuals as $inds) {
				foreach ($inds->children() as $ind) {
					//echo sprintf('CLASS: %s -> INDIVIDUAL: %s', $class->name, $ind->name) . "<br>";
					array_push($results, Array(
						'id' => $i,
						'title' => $ind->name . "",
						'parent_id' => $ids[sprintf('%s', $class->name)]
						));
					$lastInd = $i;
					$i++;
					foreach ($ind->relationships as $rels) {
						foreach ($rels->children() as $rel) {
							//echo sprintf('CLASS: %s -> INDIVIDUAL: %s -> RELATIONSHIP: %s',
							 //$class->name, $ind->name, $rel->name) . "<br>";
							array_push($results, Array(
								'id' => $i,
								'title' => $rel->name . "",
								'parent_id' => $lastInd
								));
							$i++;
						}
					}
				}
			}
			foreach ($class->necessaryConditions as $necs) {
				foreach ($necs->children() as $nec) {
					//echo sprintf('CLASS: %s -> NECCONDITION: %s', $class->name, $nec->condition) . "<br>";
					array_push($results, Array(
						'id' => $i,
						'title' => $nec->condition . "",
						'parent_id' => $ids[sprintf('%s', $class->name)]
						));
					$i++;
				}
			}
			foreach ($class->sufficientConditions as $sufs) {
				foreach ($sufs->children() as $suf) {
					//echo sprintf('CLASS: %s -> SUFCONDITION: %s', $class->name, $suf->condition) . "<br>";
					array_push($results, Array(
						'id' => $i,
						'title' => $suf->condition . "",
						'parent_id' => $ids[sprintf('%s', $class->name)]
						));
					$i++;
				}
			}
		}
/*
		$output = array();
		
		$handle = fopen($outputfile, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				array_push($output, $line);
			}
			fclose($handle);
		} else {
			array_push($output, "Error openning output file");
		} 
*/
		return $results;
	}
}
