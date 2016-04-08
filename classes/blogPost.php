<?
class blogPost{
	private $name;
	private $content;
	private $dateCreated;

	private $acceptGet = array(
		'name' => 'name',
		'content' => 'content',
		'date_created' => 'dateCreated'
	);

	public function create($name, $content){
		global $db;
		$date = date('Y-m-d H:i:s', time());
		$procedure = buildProcedure('p_blog_post_add', $name, $content, $date);
		if(($db->multi_query($procedure)) === TRUE){
			while ($db->more_results()){
				$db->next_result();
			}
			$this->name = $name;
			$this->content = $content;
			$this->dateCreated = $date;
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}

	public function loadByObj($blogPost){
		$this->name = $blogPost->name;
		$this->content = $blogPost->content;
		$this->dateCreated = $blogPost->date_created;
	}

	public function get($prpty){
		if(isset($this->name)){
			if(in_array($prpty, $this->acceptGet)){
				return $this->$prpty;
			}else{
				throw new illegalOperationException('Property is not in accept get.');
			}
		}else{
			throw new illegalFunctionCallException('Not set for get.');
		}
	}

	public static function getBlogPosts($beforeDate=null, $afterDate=null){
		$beforeDate = correctDateFormat(isset($beforeDate) ? $beforeDate : time());
		$afterDate = correctDateFormat(isset($afterDate) ? $afterDate : 0);
		global $db;
		$procedure = buildProcedure('p_get_blog_posts', $beforeDate, $afterDate);
		if(($db->multi_query($procedure)) === TRUE){
			$results = $db->store_result();
			while ($db->more_results()){
				$db->next_result();
			}
			$blogPosts = array();
			if ($results->num_rows) {
				while ($blogPostObj = $results->fetch_object()) {
					$blogPost = new blogPost();
					$blogPost->loadByObj($blogPostObj);
					$blogPosts[] = $blogPost;
				}
			}
			return $blogPosts;
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}
}