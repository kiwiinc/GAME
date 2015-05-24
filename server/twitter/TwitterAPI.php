<?php
class TwitterAPI
{
    /**
     * @var \TwitterAPIExchange
     */
    protected $exchange;
    /**
     * @var int Stores a tweet id (for /update) to be deleted later (by /destroy)
     */
    private static $tweetId;
    /**
     * @var int Stores uploaded media id
     */
    private static $mediaId;
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $settings  = array();
        $settings = array (
        		'oauth_access_token' => OAUTH_ACCESS_TOKEN,
        		'oauth_access_token_secret' => OAUTH_ACCESS_TOKEN_SECRET,
        		'consumer_key' => CONSUMER_KEY,
        		'consumer_secret' => CONSUMER_SECRET
        );
        $this->exchange = new \TwitterAPIExchange($settings);
    }
    /**
     * GET statuses/mentions_timeline
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/mentions_timeline
     */
    public function statusesMentionsTimeline()
    {
        $url    = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
        $method = 'GET';
        $params = '?max_id=595150043381915648';
        $data     = $this->exchange->request($url, $method, $params);
        $expected = "@j7php Test mention";
        
    }
    /**
     * GET statuses/user_timeline
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/user_timeline
     */
    public function statusesUserTimeline()
    {
        $url    = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $method = 'GET';
        $params = '?user_id=3223823822&count=3';
        $data     = $this->exchange->request($url, $method, $params);
        return json_decode($data);
        
    }
    /**
     * GET statuses/home_timeline
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/home_timeline
     */
    public function statusesHomeTimeline()
    {
        $url    = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
        $method = 'GET';
        $params = '?user_id=3232926711&max_id=595155660494471168';
        $data     = $this->exchange->request($url, $method, $params);
        $expected = "Test Tweet";
        
    }
    /**
     * GET statuses/retweets_of_me
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/retweets_of_me
     */
    public function statusesRetweetsOfMe()
    {
        $url    = 'https://api.twitter.com/1.1/statuses/retweets_of_me.json';
        $method = 'GET';
        $data     = $this->exchange->request($url, $method);
        $expected = 'travis CI and tests';
        
    }
    /**
     * GET statuses/retweets/:id
     *
     * @see https://api.twitter.com/1.1/statuses/retweets/:id.json
     */
    public function statusesRetweetsOfId()
    {
        $url    = 'https://api.twitter.com/1.1/statuses/retweets/595155660494471168.json';
        $method = 'GET';
        $data     = $this->exchange->request($url, $method);
        $expected = 'travis CI and tests';
        
    }
    /**
     * GET Statuses/Show/:id
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/show/:id
     */
    public function statusesShowId()
    {
        $url    = 'https://api.twitter.com/1.1/statuses/show.json';
        $method = 'GET';
        $params = '?id=595155660494471168';
        $data     = $this->exchange->request($url, $method, $params);
        $expected = 'travis CI and tests';
        
    }
    /**
     * POST media/upload
     *
     * @see https://dev.twitter.com/rest/reference/post/media/upload
     *
     * @note Uploaded unattached media files will be available for attachment to a tweet for 60 minutes
     */
    public function mediaUpload()
    {
        $file = file_get_contents(__DIR__ . '/img.png');
        $data = base64_encode($file);
        $url    = 'https://upload.twitter.com/1.1/media/upload.json';
        $method = 'POST';
        $params = array(
            'media_data' => $data
        );
        $data     = $this->exchange->request($url, $method, $params);
        $expected = 'image\/png';
        
        /** Store the media id for later **/
        $data = @json_decode($data, true);
        $this->assertArrayHasKey('media_id', is_array($data) ? $data : array());
        self::$mediaId = $data['media_id'];
    }
    /**
     * POST statuses/update
     *
     * @see https://dev.twitter.com/rest/reference/post/statuses/update
     */
    public function statusesUpdate($post)
    {
    	/*
        if (!self::$mediaId)
        {
            $this->fail('Cannot /update status because /upload failed');
        }
        */
    	$status = $post["status"];
        $url    = 'https://api.twitter.com/1.1/statuses/update.json';
        $method = 'POST';
        $params = array(
            'status' => $status,
            //'media_ids' => self::$mediaId
        );
        $data     = $this->exchange->request($url, $method, $params);
        
        /** Store the tweet id for testStatusesDestroy() **/
        $data = @json_decode($data, true);
        return $data;
        
        $this->assertArrayHasKey('id_str', is_array($data) ? $data : array());
        self::$tweetId = $data['id_str'];
        /** We've done this now, yay **/
        self::$mediaId = null;
    }
    /**
     * POST statuses/destroy/:id
     *
     * @see https://dev.twitter.com/rest/reference/post/statuses/destroy/:id
     */
    public function statusesDestroy()
    {
        if (!self::$tweetId)
        {
            $this->fail('Cannot /destroy status because /update failed');
        }
        $url    = sprintf('https://api.twitter.com/1.1/statuses/destroy/%d.json', self::$tweetId);
        $method = 'POST';
        $params = array(
            'id' => self::$tweetId
        );
        $data     = $this->exchange->request($url, $method, $params);
        $expected = 'TEST TWEET TO BE DELETED';
        
        /** We've done this now, yay **/
        self::$tweetId = null;
    }
    /**
     * GET search/tweets
     *
     * @see https://dev.twitter.com/rest/reference/get/search/tweets
     */
    public function canSearchWithHashTag()
    {
        $url    = 'https://api.twitter.com/1.1/search/tweets.json';
        $method = 'GET';
        $params = '?q=#twitter';
        $data = $this->exchange->request($url, $method, $params);
        $data = (array)@json_decode($data, true);
        
    }
    /**
     * Test to check that options passed to curl do not cause any issues
     */
    public function additionalCurlOptions()
    {
        $url    = 'https://api.twitter.com/1.1/search/tweets.json';
        $method = 'GET';
        $params = '?q=#twitter';
        $data = $this->exchange->request($url, $method, $params, array(CURLOPT_ENCODING => ''));
        $data = (array)@json_decode($data, true);
        
    }
}