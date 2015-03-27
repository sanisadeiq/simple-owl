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
		$items = array();

		foreach ($xml->class as $class) {
			//print_r($class['name'.""]. '<br>');
			$ids[$class['name'].""] = $i;
			$i++;
		}

		foreach ($xml->class as $class) {
			//echo sprintf('parent: %s', $class['parent']) . "<br>";
			array_push($items, Array(
				'id' => $ids[$class['name'].""],
				'title' => $class['name'] . "",
				'parent_id' => $ids[$class['parent'].""]
				));
			foreach ($class->individual as $ind) {
				//echo sprintf('CLASS: %s -> INDIVIDUAL: %s', $class->name, $ind->name) . "<br>";
				array_push($items, Array(
					'id' => $i,
					'title' => $ind['name'] . "",
					'parent_id' => $ids[$class['name'].""]
					));
				$lastInd = $i;
				$i++;
				foreach ($ind->relationship as $rel) {
					//echo sprintf('CLASS: %s -> INDIVIDUAL: %s -> RELATIONSHIP: %s',
					 //$class->name, $ind->name, $rel->name) . "<br>";
					array_push($items, Array(
						'id' => $i,
						'title' => $rel['name'] . " " . $rel,
						'parent_id' => $lastInd
						));
					$i++;
				}
			}
			foreach ($class->necessaryCondition as $nec) {
				//echo sprintf('CLASS: %s -> NECCONDITION: %s', $class->name, $nec->condition) . "<br>";
				array_push($items, Array(
					'id' => $i,
					'title' => $nec['name'] . "",
					'parent_id' => $ids[sprintf('%s', $class['name'])]
					));
				$i++;
			}
			foreach ($class->sufficientCondition as $suf) {
				//echo sprintf('CLASS: %s -> SUFCONDITION: %s', $class->name, $suf->condition) . "<br>";
				array_push($items, Array(
					'id' => $i,
					'title' => $suf['name'] . "",
					'parent_id' => $ids[sprintf('%s', $class['name'])]
					));
				$i++;
			}
		}

		return view('treeview', ['items' => $items]);
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
		$items = array();

		foreach ($xml->class as $class) {
			//print_r($class['name'.""]. '<br>');
			$ids[$class['name'].""] = $i;
			$i++;
		}

		foreach ($xml->class as $class) {
			//echo sprintf('parent: %s', $class['parent']) . "<br>";
			array_push($items, Array(
				'id' => $ids[$class['name'].""],
				'title' => $class['name'] . "",
				'parent_id' => $ids[$class['parent'].""],
				'active' => true
				));
			foreach ($class->individual as $ind) {
				//echo sprintf('CLASS: %s -> INDIVIDUAL: %s', $class->name, $ind->name) . "<br>";
				array_push($items, Array(
					'id' => $i,
					'title' => $ind['name'] . "",
					'parent_id' => $ids[$class['name'].""],
				'active' => true
					));
				$lastInd = $i;
				$i++;
				foreach ($ind->relationship as $rel) {
					//echo sprintf('CLASS: %s -> INDIVIDUAL: %s -> RELATIONSHIP: %s',
					 //$class->name, $ind->name, $rel->name) . "<br>";
					array_push($items, Array(
						'id' => $i,
						'title' => $rel['name'] . " " . $rel,
						'parent_id' => $lastInd,
						'active' => true
						));
					$i++;
				}
			}
			foreach ($class->necessaryCondition as $nec) {
				//echo sprintf('CLASS: %s -> NECCONDITION: %s', $class->name, $nec->condition) . "<br>";
				array_push($items, Array(
					'id' => $i,
					'title' => $nec['name'] . "",
					'parent_id' => $ids[sprintf('%s', $class['name'])],
					'active' => true
					));
				$i++;
			}
			foreach ($class->sufficientCondition as $suf) {
				//echo sprintf('CLASS: %s -> SUFCONDITION: %s', $class->name, $suf->condition) . "<br>";
				array_push($items, Array(
					'id' => $i,
					'title' => $suf['name'] . "",
					'parent_id' => $ids[sprintf('%s', $class['name'])],
					'active' => true
					));
				$i++;
			}
		}

		foreach ($items as $item) {
			$item['subs'] = array();
			$indexedItems[$item['id']] = (object) $item;
		}


		//assign to parent
		$topLevel = array();
		foreach ($indexedItems as $item) {
			if ($item->parent_id == 0) {
				$topLevel[] = $item;
			} else {
				$indexedItems[$item->parent_id]->subs[] = $item;
			}
		}

		return $topLevel;
	}
}
