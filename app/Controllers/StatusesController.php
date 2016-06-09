<?php
namespace Controllers;

use Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\RouteCollection;
use Cartalyst\Sentinel\Native\Facades\Sentinel;


class StatusesController extends BaseController
{

    /**
     * Create new status
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createAction(Request $request)
    {
        if($user = Sentinel::check()) {
            $post = Post::create([
                'text' => $request->get('text'),
                'user_id' => $user->id
            ]);

            if($post) {
                return $this->successResponse('Post successfully created');
            } else {
                return $this->errorResponse('An error occurred');
            }
        }

        return $this->errorResponse('Authorization required');
    }


    /**
     * Get status by status id if exists
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getStatusAction($id)
    {
        $post = Post::find($id);

        if($post) {
            return $this->successResponse('Status Found', ['result' => $post->getPostDataObject()]);
        }

        return $this->errorResponse('Status not found');
    }


    /**
     * Get statuses from user
     * @param RouteCollection $route
     * @param Request $request
     * @param $user_id
     * @param $limit
     * @param $offset
     * @param int $page
     * @return \Illuminate\Http\Response
     */
    public function getUserStatusesAction(RouteCollection $route, Request $request, $user_id, $limit, $offset, $page = 1)
    {

        $offset_or = $offset;

        if($offset < 1 && $page > 1) {
            $offset = ($limit * $page) - $limit;
        } elseif ($page > 1) {
            $offset = $offset * $page;
        }

        $posts = Post::where(['user_id' => $user_id])->orderBy('id');
        $total = $posts->count();
        $posts = $posts->take($limit)->offset($offset)->get()->all();

        if($posts) {
            $url = new UrlGenerator($route, $request);

            $next_page = floor(($total - $offset_or) / $limit);
            $next_page = $page < $next_page ? $page + 1 : null;
            $next_link = $next_page != null ? $url->to('statuses/user-timeline/', ['user_id' => $user_id, 'limit' => $limit, 'offset' => $offset, 'page' => $next_page]) : $next_page;

            $prev_page = $page > 1 ? $page - 1: null;
            $prev_link = $prev_page != null ? $url->to('statuses/user-timeline/', ['user_id' => $user_id, 'limit' => $limit, 'offset' => $offset, 'page' => $prev_page]) : $prev_page;

            $results = [];
            foreach($posts as $post) {
//                $results[] = $post->getPostDataObject();
                $results[] = $post->getId();
            }

            $aditional = [
                'status' => 'success',
                'message' => 'Statuses found',
                'results' => $results,
                'links' => [
                    'self' => $url->current(),
                    'next' => $next_link,
                    'prev' => $prev_link,
                ],
                'total' => $total
            ];

            return $this->successResponse('Statuses found', $aditional);
        } else {
            return $this->errorResponse('Nothing to show');
        }
    }


    /**
     * Get all statues
     * @param RouteCollection $route
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getStatusesHomeAction(RouteCollection $route, Request $request, $limit, $offset, $page = 1)
    {
        $offset_or = $offset;

        if($offset < 1 && $page > 1) {
            $offset = ($limit * $page) - $limit;
        } elseif ($page > 1) {
            $offset = $offset * $page;
        }
        $posts = new Post();
        $total = $posts->count();
        $posts = $posts->take($limit)->offset($offset)->get()->all();

        $results = [];
        foreach($posts as $post) {
            $results[] = $post->getPostDataObject();
        }

        if($posts) {
            $url = new UrlGenerator($route, $request);
            $next_page = floor(($total - $offset_or) / $limit);
            $next_page = $page < $next_page ? $page + 1 : null;
            $next_link = $next_page != null ? $url->to('statuses/home/', ['limit' => $limit, 'offset' => $offset, 'page' => $next_page]) : $next_page;

            $prev_page = $page > 1 ? $page - 1: null;
            $prev_link = $prev_page != null ? $url->to('statuses/home/', ['limit' => $limit, 'offset' => $offset, 'page' => $prev_page]) : $prev_page;

            $aditional = [
                'results' => $results,
                'links' => [
                    'self' => $url->current(),
                    'next' => $next_link,
                    'prev' => $prev_link,
                ],
                'total' => $total
            ];
            return $this->successResponse('Statuses found', $aditional);
        }

        return $this->errorResponse('No statuses');
    }

}