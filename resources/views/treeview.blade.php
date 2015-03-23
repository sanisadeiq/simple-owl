<html ng-app="owl">
<head>
	<title>TreeView Spike</title>
	<link rel="stylesheet" type="text/css" href={{ URL::asset('css/style.css') }}>
	<link rel="stylesheet" type="text/css" href={{ URL::asset('css/bootstrap-combined.min.css') }}>
	<script src={{ URL::asset('js/jquery/jquery-2.1.3.min.js') }}></script>
	<script src={{ URL::asset('js/bootstrap.min.js') }}></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>
	<script type="text/javascript" src="js/app.js"></script>
	<script type="text/javascript">
		$(function () {
			$('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
			$('.tree li.parent_li > span').on('click', function (e) {
				var children = $(this).parent('li.parent_li').find(' > ul > li');
				if (children.is(":visible")) {
					children.hide('fast');
					$(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
				} else {
					children.show('fast');
					$(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
				}
				e.stopPropagation();
			});
		});


		window.onload = function start() {
			//myLoop();
		}
		
		var i = 1;

		function myLoop () {           //  create a loop function
		   setTimeout(function () {    //  call a 3s setTimeout when the loop is called
	         //  your code here
		      i++;                     //  increment the counter
		      if (i < 10) {            //  if the counter < 10, call the loop function
		         myLoop();             //  ..  again which will trigger another 
		      }                        //  ..  setTimeout()
		  }, 3000)
		}
	</script>
</head>
<body ng-controller="TreeviewController">
	<p>[[output]]</p>
	<div class="tree well">
		<?php 

		$items = Array
		(
			Array
			(
				'id' => 1,
				'title' => 'menu1',
				'parent_id' => 0
				),
			Array
			(
				'id' => 2,
				'title' => 'submenu1-1',
				'parent_id' => 1
				),
			Array
			(
				'id' => 3,
				'title' => 'submenu1-2',
				'parent_id' => 1
				),
			Array
			(
				'id' => 4,
				'title' => 'menu2',
				'parent_id' => 0
				),
			Array
			(
				'id' => 5,
				'title' => 'submenu2-1',
				'parent_id' => 4
				),
			Array
			(
				'id' => 6,
				'title' => 'submenu3-1',
				'parent_id' => 3
				)
			);

//index elements by id
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

//recursive function
		function renderMenu($items) {
			$render = '<ul>';

			foreach ($items as $item) {
				$render .= '<li><span>' . $item->title . '</span>';
				if (!empty($item->subs)) {
					$render .= renderMenu($item->subs);
				}
				$render .= '</li>';
			}

			return $render . '</ul>';

		}

		echo renderMenu($topLevel);

		?>
<!--
		<ul>
			<li>
				<span><i class="icon-folder-open"></i> Parent</span> <a href="">Goes somewhere</a>
				<ul>
					<li>
						<span><i class="icon-minus-sign"></i> Child</span> <a href="">Goes somewhere</a>
						<ul>
							<li>
								<span><i class="icon-leaf"></i> Grand Child</span> <a href="">Goes somewhere</a>
							</li>
						</ul>
					</li>
					<li>
						<span><i class="icon-minus-sign"></i> Child</span> <a href="">Goes somewhere</a>
						<ul>
							<li>
								<span><i class="icon-leaf"></i> Grand Child</span> <a href="">Goes somewhere</a>
							</li>
							<li>
								<span><i class="icon-minus-sign"></i> Grand Child</span> <a href="">Goes somewhere</a>
								<ul>
									<li>
										<span><i class="icon-minus-sign"></i> Great Grand Child</span> <a href="">Goes somewhere</a>
										<ul>
											<li>
												<span><i class="icon-leaf"></i> Great great Grand Child</span> <a href="">Goes somewhere</a>
											</li>
											<li>
												<span><i class="icon-leaf"></i> Great great Grand Child</span> <a href="">Goes somewhere</a>
											</li>
										</ul>
									</li>
									<li>
										<span><i class="icon-leaf"></i> Great Grand Child</span> <a href="">Goes somewhere</a>
									</li>
									<li>
										<span><i class="icon-leaf"></i> Great Grand Child</span> <a href="">Goes somewhere</a>
									</li>
								</ul>
							</li>
							<li>
								<span><i class="icon-leaf"></i> Grand Child</span> <a href="">Goes somewhere</a>
							</li>
						</ul>
					</li>
				</ul>
			</li>
			<li>
				<span><i class="icon-folder-open"></i> Parent2</span> <a href="">Goes somewhere</a>
				<ul>
					<li>
						<span><i class="icon-leaf"></i> Child</span> <a href="">Goes somewhere</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>

	<div class="tree">
		<ul>
			<li>
				<span><i class="icon-calendar"></i> 2013, Week 2</span>
				<ul>
					<li>
						<span class="badge badge-success"><i class="icon-minus-sign"></i> Monday, January 7: 8.00 hours</span>
						<ul>
							<li>
								<a href=""><span><i class="icon-time"></i> 8.00</span> &ndash; Changed CSS to accomodate...</a>
							</li>
						</ul>
					</li>
					<li>
						<span class="badge badge-success"><i class="icon-minus-sign"></i> Tuesday, January 8: 8.00 hours</span>
						<ul>
							<li>
								<span><i class="icon-time"></i> 6.00</span> &ndash; <a href="">Altered code...</a>
							</li>
							<li>
								<span><i class="icon-time"></i> 2.00</span> &ndash; <a href="">Simplified our approach to...</a>
							</li>
						</ul>
					</li>
					<li>
						<span class="badge badge-warning"><i class="icon-minus-sign"></i> Wednesday, January 9: 6.00 hours</span>
						<ul>
							<li>
								<a href=""><span><i class="icon-time"></i> 3.00</span> &ndash; Fixed bug caused by...</a>
							</li>
							<li>
								<a href=""><span><i class="icon-time"></i> 3.00</span> &ndash; Comitting latest code to Git...</a>
							</li>
						</ul>
					</li>
					<li>
						<span class="badge badge-important"><i class="icon-minus-sign"></i> Wednesday, January 9: 4.00 hours</span>
						<ul>
							<li>
								<a href=""><span><i class="icon-time"></i> 2.00</span> &ndash; Create component that...</a>
							</li>
						</ul>
					</li>
				</ul>
			</li>
			<li>
				<span><i class="icon-calendar"></i> 2013, Week 3</span>
				<ul>
					<li>
						<span class="badge badge-success"><i class="icon-minus-sign"></i> Monday, January 14: 8.00 hours</span>
						<ul>
							<li>
								<span><i class="icon-time"></i> 7.75</span> &ndash; <a href="">Writing documentation...</a>
							</li>
							<li>
								<span><i class="icon-time"></i> 0.25</span> &ndash; <a href="">Reverting code back to...</a>
							</li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	-->

	<?php
/*
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
		
		if(isRunning($pidArr[0])){
			echo "Loading tree..";
		}*/

		/*
		while(isRunning($pidArr[0])){
			echo ".";
			ob_flush(); flush();
			sleep(1);
		}*/


		
		
		//array to string




// $oldcwd = getcwd();
// chdir("C:/");
// $output = shell_exec('dir');

 // chdir($oldcwd); 

//$output = exec('java -jar C:/agent.jar', $output);

//shell_exec('java -jar C:/agent.jar')
			//$output =	shell_exec("java -jar agent.jar server");
//system("c:\\agent.jar\");
	//echo '<pre>Output: '.$output.'</pre>';
		?>
	</div>
</body>
</html>
