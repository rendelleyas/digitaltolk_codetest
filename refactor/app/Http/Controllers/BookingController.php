<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if($user_id = $request->get('user_id')) {

            $response = $this->repository->getUsersJobs($user_id);

        }
        elseif($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
        {
            $response = $this->repository->getAll($request);
        }

        return response($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->repository->with('translatorJobRel.user')->find($id);

        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $data = $request->all();
        return response($this->repository->store($request->__authenticatedUser, $data));

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $data = $request->all();
        $cuser = $request->__authenticatedUser;
        return response($this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $data = $request->all();
        return response($this->repository->storeJobEmail($data));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        if($user_id = $request->get('user_id')) {
            return response($this->repository->getUsersJobsHistory($user_id, $request));
        }
        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;
        return response($this->repository->acceptJob($data, $user));
    }

    public function acceptJobWithId(Request $request)
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        return response($this->repository->acceptJobWithId($data, $user));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;
        return response($this->repository->cancelJobAjax($data, $user));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $data = $request->all();
        return response($this->repository->endJob($data));

    }

    public function customerNotCall(Request $request)
    {
        $data = $request->all();
        return response($this->repository->customerNotCall($data));

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;
        return response($this->repository->getPotentialJobs($user));
    }

    public function distanceFeed(Request $request)
    {
        $data = $request->all();

        $distance = isset($data['distance']) && $data['distance'] != ""? $data['distance'] : "";

        $time = isset($data['time']) && $data['time'] != ""? $data['time'] : "";
        
        $jobId = isset($data['jobid']) && $data['jobid'] != ""? $data['jobid'] : "";

        $session = isset($data['session_time']) && $data['session_time'] != ""? $data['session_time'] : "";
       

        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }

        $manually_handled = $data['manually_handled'] == 'true'? "yes" : "no";

        $by_admin = $data['by_admin'] == 'true'? "yes" : "no";
        
        $admincomment = isset($data['admincomment']) && $data['admincomment'] != ""? $data['admincomment'] : "";
       
        if ($time || $distance) {
            Distance::where('job_id', '=', $jobId)
                ->update(array('distance' => $distance, 'time' => $time));
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            Job::where('id', '=', $jobId)
                ->update(array(
                    'admin_comments' => $admincomment, 
                    'flagged' => $flagged, 
                    'session_time' => $session, 
                    'manually_handled' => $manually_handled, 
                    'by_admin' => $by_admin
                ));
        }

        return response('Record updated!');
    }

    public function reopen(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->reopen($data);

        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $this->repository->sendNotificationTranslator($job, $this->repository->jobToData($job), '*');
        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $this->repository->jobToData($job);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
