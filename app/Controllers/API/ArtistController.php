<?php
namespace API;

class ArtistController extends \BaseController {
    private $artistModel;
    private $paintingModel;
    
    public function __construct() {
        parent::__construct();
        $this->artistModel = new \Artist();
        $this->paintingModel = new \Painting();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
    
    public function index() {
        try {
            SecurityManager::rateLimit('api_artists', 200, 3600);
            
            $artists = $this->artistModel->getActiveWithPaintingCount();
            
            $this->json([
                'success' => true,
                'data' => $artists
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Unable to fetch artists'], 500);
        }
    }
    
    public function paintings($artistId) {
        try {
            SecurityManager::rateLimit('api_artist_paintings', 200, 3600);
            
            $paintings = $this->paintingModel->getByArtist($artistId);
            
            $this->json([
                'success' => true,
                'data' => $paintings
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Unable to fetch paintings'], 500);
        }
    }
}
?>