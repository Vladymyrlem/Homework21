<?php


namespace App\Http\Controllers\API;


use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *    title="CRUD ApplicationAPI",
 *    version="1.0.0",
 *     description="Project Controller for CRUD API"
 * )
 */
class ProjectController
{
    /**
     * @OA\Post(
     * path="api/users/projects",
     * summary="Create project",
     * description="Creating project",
     * operationId="store",
     * tags={"projects"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="param project",
     *    @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example="Project_1"),
     *       @OA\Property(property="author_id", type="int", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Project created",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $requests = $request->all();

        $validator = Validator::make($requests,
            ['*.name' => ['required', 'unique:projects,name', 'min:5']]
        )->validate();

        foreach ($requests as $data) {
            $user_id = Auth::user()->getAuthIdentifier();
            $data['user_id'] = $user_id;
            $project = Project::create($data);

            $project->linkedUsers()->attach($user_id);
        }

        return response(['status:' => 'ok']);
    }

    /**
     * @OA\Post(
     * path="api/projects/link/users",
     * summary="Display user by project",
     * description="Displaying user by project id",
     * operationId="project_id",
     * tags={"projects"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="param project",
     *    @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example="Project_1"),
     *       @OA\Property(property="project_id", type="int", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Link To users by project id",
     *     )
     * )
     * @param Request $request
     */
    public function linkUsers(Request $request)
    {
        $requests = $request->all();

        foreach ($requests as $data) {
            $project = Project::find($data['project_id']);
            $project->linkedUsers()->attach($data['users']);
        }

        return response(['status:' => 'ok']);
    }

    /**
     * @OA\Get (
     * path="api/projects",
     * summary="Get projects",
     * description="Get projects",
     * operationId="get_projects",
     * tags={"projects"},
     * security={ {"bearer": {} }},
     *
     * @OA\Response(
     *    response=200,
     *    description="Response JSON array",
     *     @OA\JsonContent(
     *       @OA\Property(property="id", type="int", example="1"),
     *       @OA\Property(property="name", type="string", example="Project_name"),
     *       @OA\Property(property="author", type="string", example="Volodymyr"),
     *       @OA\Property(property="labels", type="JSON", example="['New Label','new_Label']"),
     *    )
     *   )
     * )
     * Display the specified resource.
     *
     * @param Request $request
     */
    public function list(Request $request)
    {
        $query = Project::query()->select('projects.*')
            ->join('project_user', 'projects.id', '=',
                'project_user.project_id')
            ->where('project_user.user_id', auth()->id());


        if ($request->has('email')) {
            $query->join('users', 'projects.user_id', '=', 'users.id')
                ->where('email', '=', $request->get('email'));

        }

        if ($request->has('labels')) {
            $query->join('label_project', 'projects.id', '=',
                'label_project.project_id')
                ->whereIn('label_id', $request->get('labels'));

        }

        if ($request->has('continent')) {
            $query->join('countries', 'users.country_id', '=', 'countries.id')
                ->join('continents',
                    'countries.continent_id', '=', 'continents.id')
                ->where('continents.code', '=', $request->get('continent'));

        }


        return ProjectResource::collection($query->distinct()->get());
    }

    /**
     * @OA\Delete (
     * path="api/projects",
     * summary="Delete project to users",
     * description="Delete project to users",
     * operationId="delete_project",
     * tags={"projects"},
     * security={ {"bearer": {} }},
     *
     *     @OA\Parameter (
     *        in="path",
     *        name="projectId",
     *        required=true,
     *        example="1",
     *           @OA\Schema(
     *               type="integer",
     *               format="int"
     *               )
     *      ),
     * @OA\Response(
     *    response=200,
     *    description="Successfully removed",
     *   )
     *  )
     *
     * Remove the specified resource from storage.
     *
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        $requests = $request->all();

        foreach ($requests as $data) {

            $project = Project::find($data);

            abort_if($request->user()->cannot('delete', $project), 403, 'you can not delete this projects');

            $project->delete();
        }

        return response(['status:' => 'ok']);
    }
}
