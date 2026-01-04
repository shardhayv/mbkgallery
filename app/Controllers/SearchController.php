<?php
class SearchController extends BaseController {
    private $searchService;
    
    public function __construct() {
        parent::__construct();
        $this->searchService = new SearchService();
    }
    
    public function index() {
        $query = $_GET['q'] ?? '';
        $filters = [
            'category' => $_GET['category'] ?? '',
            'artist' => $_GET['artist'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'sort' => $_GET['sort'] ?? 'created_at',
            'order' => $_GET['order'] ?? 'DESC',
            'page' => max(1, intval($_GET['page'] ?? 1)),
            'limit' => 12
        ];
        
        $paintings = $this->searchService->searchPaintings($query, $filters);
        $totalCount = $this->searchService->getSearchCount($query, $filters);
        $searchFilters = $this->searchService->getSearchFilters();
        
        $totalPages = ceil($totalCount / $filters['limit']);
        
        $this->view('search/index', [
            'paintings' => $paintings,
            'query' => $query,
            'filters' => $filters,
            'searchFilters' => $searchFilters,
            'totalCount' => $totalCount,
            'currentPage' => $filters['page'],
            'totalPages' => $totalPages
        ]);
    }
    
    public function api() {
        $query = $_GET['q'] ?? '';
        $filters = [
            'category' => $_GET['category'] ?? '',
            'artist' => $_GET['artist'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'sort' => $_GET['sort'] ?? 'created_at',
            'order' => $_GET['order'] ?? 'DESC',
            'page' => max(1, intval($_GET['page'] ?? 1)),
            'limit' => intval($_GET['limit'] ?? 12)
        ];
        
        $paintings = $this->searchService->searchPaintings($query, $filters);
        $totalCount = $this->searchService->getSearchCount($query, $filters);
        
        $this->json([
            'success' => true,
            'data' => $paintings,
            'total' => $totalCount,
            'page' => $filters['page'],
            'limit' => $filters['limit'],
            'totalPages' => ceil($totalCount / $filters['limit'])
        ]);
    }
}
?>