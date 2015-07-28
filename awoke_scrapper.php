<?php
// include simple html dom parser class
require_once 'html_parser/simple_html_dom.php';
/*
* Amazon Product Search class
*/
class Amazon_Product_Search{
    protected $url = 'http://www.amazon.com/s/?url=search-alias%3Daps&field-keywords=';
	protected $keyword='';
	
	public function __construct($keyword){
		$this->url = $this->url.$keyword;
	}
    
    public function get(){
		$products =  array();
		// Prices array
		$ps	  =  array();
        try{
            $webPageContent = $this->getWebPageContent();
			if ( !empty( $webPageContent ) ){
				$html 	= str_get_html($webPageContent);
				
				foreach($html->find('li.s-result-item') as $key => $li) {
					$li = $this->appendTags($li);	
					foreach($li->find('img.s-access-image') as  $element){
						 $products[$key]['images'] = 	$element->src;
					}
					foreach($li->find('a.h2.s-access-title') as $element){
						 $products[$key]['titles'] =	$element->plaintext;	
					}
					foreach($li->find('a.span.s-price') as $element){
						 $price = !empty($element->plaintext)?$element->plaintext:'0';
						 $products[$key]['prices'] =	$price;	
					}
				}
				// Sort products on the bases of prices 
				foreach ($products as $key => $row){
					$row['prices'] = !empty($row['prices'])?$row['prices']:0;
					$ps[$key] = str_replace('$','',$row['prices']);
				}
				array_multisort($ps, SORT_DESC, $products);
			}
			
			
			return $products;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
	/*
	* Fetch the content from resource url
	*/
    protected function getWebPageContent() {
		if (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			set_time_limit(0);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		}else{	
			return "";
		}
	}
	/*
	* Utility funtion to append html tags
	*/
	public function appendTags($html){
		return str_get_html('<html>'.$html.'</html>');
	}
	/*
	* Print the array results in readable form
	*/
	public function pre($args){
		echo '<pre>';
		print_r($args);
		echo '</pre>';
	}
	
}

if(isset($_REQUEST['search_word'])){
	$search_word = trim($_REQUEST['search_word']);
	$aps 	= new Amazon_Product_Search($search_word);
	$result = $aps->get();
}
// Buffer the output
ob_start();
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<style>
.city {
    float: left;
    margin: 5px;
    padding: 15px;
    width: 300px;
    height: 300px;
    border: 1px solid black;
} 
</style>
</head>
<body>
<?php 
	if(!empty($result)){
		echo '<h1>Amazon Products</h1>';
		foreach($result as $key => $products) {
			?>
			<div class="city">
			  <h4><?php echo $products['titles'];?></h4>
			  <p><img src="<?php echo $products['images'];?>" /></p>
			  <p> Price : <?php echo !empty($products['prices'])?$products['prices']:0;?></p>
			</div>
			<?php 
		}
	}else{
		echo 'No products found';
	}	
	?>
</body>
</html>
<?php
	$content = ob_get_contents();
	ob_clean();
	echo $content;
	die();
?>