<?php
namespace Controllers;

use Models\User;
use Tx\Validator;
use Models\UserFollowers;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class UserController extends BaseController
{

    protected $cover_img_dir;
    protected $profile_img_dir;

    public function __construct()
    {
        $this->cover_img_dir = __DIR__ . '/../../public/uploads/cover';
        $this->profile_img_dir = __DIR__ . '/../../public/uploads/profile';
    }


    /**
     * Get user by id
     * @param $id
     * @param bool $big_data
     * @return \Illuminate\Http\Response
     */
    public function getUserAction($id, $big_data = true)
    {
        $user = User::where(['id' => $id])->first();

        if(!$user) {
            return $this->errorResponse('Not found');
        }

        $big_data = filter_var($big_data, FILTER_VALIDATE_BOOLEAN);

        $user_obj = $big_data ? $user->getBigUserObject() : $user->getSmallUserObject();

        return $this->successResponse('User found', ['result' => $user_obj]);
    }


    /**
     * Edit user data
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function editAction(Request $request, $id)
    {
        $current_user = Sentinel::check();

        if(!$current_user || $current_user->id != $id) {
            return $this->errorResponse('Authorization required');
        }
        $user = User::find($id);

        if($user) {
            $validator = Validator::make($request->all(),[
                'display_name' => 'string|max:100',
                'description' => 'string',
                'profile_image' => 'mimes:jpeg,png',
                'cover_image' => 'mimes:jpeg,png',
            ], $this->validation_messages);

            if($validator->fails()) {
                return $this->errorResponse('Validation failed');
            }


            if($request->get('display_name'))
                $user->display_name = $request->get('display_name');

            if($request->get('description'))
                $user->description = $request->get('description');

            if($request->hasFile('cover_image')) {

                if(file_exists($this->cover_img_dir)) {

                    $cover_image = $request->file('cover_image');

                    $filename = md5(microtime() . $cover_image->getClientOriginalName());
                    $filename .= "." . $cover_image->getClientOriginalExtension();

                    if($cover_image->move($this->cover_img_dir, $filename)) {
                        // delete old image
                        (new Filesystem())->delete($this->cover_img_dir . '/' . $user->cover_image);
                        // save img name in database
                        $user->cover_image  = $filename;
                    }

                }

            }

            if($request->hasFile('profile_image')) {

                // check if folder exists
                if(file_exists($this->profile_img_dir)) {

                    $profile_image = $request->file('profile_image');

                    $filename = md5(microtime() . $profile_image->getClientOriginalName());
                    $filename .= "." . $profile_image->getClientOriginalExtension();

                    if($profile_image->move($this->profile_img_dir, $filename)) {
                        // delete old image
                        (new Filesystem())->delete($this->profile_img_dir . '/' . $user->profile_image);
                        // save img name in database
                        $user->profile_image  = $filename;
                    }

                }

            }

            if($user->save()) {
                return $this->successResponse('User data saved');
            }

            return $this->errorResponse('An error occurred while saving user data please try again');
        }

        return $this->errorResponse('User not exists');
    }


    /**
     * Search user by display_name
     * @param null $query display_name
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Http\Response
     */
    public function searchAction($query = null, $limit = 0, $offset = 0)
    {
        if($query) {
            $users = User::where(['display_name' => $query]);

            if($users) {
                $results = [];

                foreach($users->get() as $user) {
                    $results[] = $user->getSmallUserObject();
                }

                return $this->successResponse('User found', ['results' => $results]);
            }

            return $this->errorResponse('No users found');
        }

        return $this->errorResponse('Provide display name');
    }


    /**
     * Follow user
     * @param $id User to follow
     * @return \Illuminate\Http\Response
     */
    public function followAction($id)
    {
        $current_user = Sentinel::check();

        if(!$current_user) {
            return $this->errorResponse('Authorization required');
        }

        $user_to_follow = User::find($id);

        if($user_to_follow) {
            $user_followers = UserFollowers::create(['user_id' => $current_user->id, 'user_following_id' => $user_to_follow->id]);

            if($user_followers) {
                return $this->successResponse('You are following this user');
            }
        }

        return $this->errorResponse('User not found');
    }


    /**
     * Unfollow user
     * @param $id User to unfollow
     * @return \Illuminate\Http\Response
     */
    public function unfollowAction($id)
    {
        $current_user = Sentinel::check();

        if(!$current_user) {
            return $this->errorResponse('Authorization required');
        }

        $user_to_unfollow = User::find($id);

        if($user_to_unfollow) {
            if(User::find($current_user->id)->userFollowers()->detach($user_to_unfollow)) {
                return $this->successResponse('You have unfollowed this user');
            }
        }

        return $this->errorResponse('User not found');
    }
}