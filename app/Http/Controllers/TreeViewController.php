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
		return view('treeview');
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

		$output = array();
		
		$handle = fopen($outputfile, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				array_push($output, array('line1' => $line));
			}
			fclose($handle);
		} else {
			array_push($output, array('line1' => "Error openning output file"));
		} 
		
		return Response::json($output);
	}
}
