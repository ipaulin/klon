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
        $posts = Post::where(['user_id' => $user_id]);
        $total = $posts->count();
        $posts = $posts->take($limit)->offset($offset)->get()->all();

        if($posts) {
            $url = new UrlGenerator($route, $request);
            $results = [];
            foreach($posts as $post) {
                $results[] = $post->getPostDataObject();
            }

            $aditional = [
                'status' => 'success',
                'message' => 'Statuses found',
                'results' => $results,
                'links' => [
                    'self' => $url->current(),
                    'next' => '' ,
                    'prev' => '',
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
    public function getStatusesHomeAction(RouteCollection $route, Request $request)
    {
        $posts = Post::all();
        $total = $posts->count();
        $url = new UrlGenerator($route, $request);

        $results = [];
        foreach($posts as $post) {
            $results[] = $post->getPostDataObject();
        }

        if($posts) {
            $aditional = [
                'results' => $results,
                'links' => [
                    'self' => $url->current(),
                    'next' => '' ,
                    'prev' => '',
                ],
                'total' => $total
            ];
            return $this->successResponse('Statuses found', $aditional);
        }

        return $this->errorResponse('No statuses');
    }

}