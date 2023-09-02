<?php

namespace Axistrustee\ComplianceOverview\Http\Controllers;
use Spatie\Activitylog\Models\Activity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Axistrustee\ComplianceOverview\Models\Compliancetool;
use Axistrustee\ComplianceOverview\Models\ComplianceInstance;
use Axistrustee\ComplianceOverview\Models\ComplianceReminder;
use Axistrustee\ComplianceOverview\Models\ComplianceCovenant;
use Axistrustee\ComplianceOverview\Models\ComplianceClcode;
use Axistrustee\ComplianceOverview\Models\ComplianceIsin;
use Axistrustee\ComplianceOverview\Models\CustomActivityLog;
use Storage; 
use Illuminate\Support\Facades\fileFromStorage;
use File;
use Response;
use Aws\Credentials\CredentialProvider;
use Aws\Credentials\InstanceProfileProvider;
use Aws\Credentials\AssumeRoleCredentialProvider;
use Aws\S3\S3Client;
use Aws\Sts\StsClient;
//use Illuminate\Http\UploadedFile;

class ComplianceController extends Controller
{

    public function create(Request $request)
    {
        return inertia('Create');
    }

    public function addCovenant($id)
    {
        return inertia('Create',[
            'load' => 'covenantData',
            'complianceId' => $id,
            'action' => 'add',
        ]);
        //return inertia::render('Create');
    }

    public function addTracking(Request $request)
    {
        //print_r($request->all());die;
        $data = [];
        if(!empty($request->input('data')) && $request->input('action') == 'trigger-covenant') {
            $data = $request->input('data');
        }
        //print_r($data);
        return inertia('Create',[
            'load' => 'covenantData',
            'complianceId' => $request->input('complianceId'),
            'covenantId' => $request->input('covenantId'),
            'action' => $request->input('action'),
            'data' => $data
        ]);
        //return inertia::render('Create');
    }

    public function clcodeImport(Request $request)
    {
        //print_r($request->all());die;
        //echo $request->clcodefile[0]->getMimeType();die;
        $validated = $request->validate([
            //'clcodefile.0' => 'required|file|min:1|mimes:csv',
            'clcodefile.0' => 'required|file|mimes:csv,txt',
        ],
        [
            'clcodefile.0.*' => 'Invalid file uploaded for Clcodes',
        ]);
        $header = null;
        $data = array();
        $delimiter = ",";
        $filename = $request->clcodefile[0];
        $current_user = \Auth::user()->id;
        $org_id = DB::table('users')->where('id', $current_user)->value('organization_id');
        if (($handle = fopen($filename, 'r')) !== false)
        {
            $i = 1;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
                
                if($i >= 2) {

                    $alreadyPresent = ComplianceClcode::where('clcode', $row[1])->where('isin', $row[0])->where('organization_id', $org_id)->first();
                
                    if($alreadyPresent == null) {
                        DB::table('clcode_isins')->insert([
                        'clcode' => $row[1],
                        'isin' => $row[0],
                        'organization_id' => $org_id
                        ]);
                    }
                }

                $i++;
            }
            fclose($handle);
        }
        $this->fetchClcode();
        //print_r($data);die;
    }

    public function edit(Request $request, $id)
    {
        
        try {
                $compliance = Compliancetool::FindOrFail($id);


                $isins = DB::table('compliances_isin')
                        ->select('isin')
                        ->where('complianceId',$id)
                        ->get();

                $isin_arr = [];
                if(count($isins) > 0) {
                    foreach ($isins as $key => $isin) {
                        $isin_arr[$key] = $isin->isin;
                    }
                }
                //print_r($isin_arr);die;
                $filenames = json_decode($compliance->documentNames);
                /*$s3 = Storage::disk('s3');
                foreach ($filenames as $key => $filename) {
                    $file[$key]['path'] = $s3->url($filename);
                    $file[$key]['filename'] = $filename;
                }*/
                $compliance->documentNames = $filenames;
                $compliance->isinValues = $isin_arr;
            

        } catch (\Exception $e) {
            $compliance =  response()->json(['message'=>'user not found!'], 404);
        }

        return inertia('EditOverview',[
            'compliance' => $compliance,
        ]);
    }

    public function attachment(Request $request)
    {
        //echo "here";
        //print_r($request->all());die;
        $filename = $request->input('file');
        $url = Storage::disk('s3')->url($filename);

        $headers = [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="'. $filename .'"',
        ];
        //$content = Storage::disk('s3')->get($url);
        $profile = new InstanceProfileProvider();
        $ARN = "arn:aws:iam::510716447920:role/covenantRole";
        $sessionName = "s3-access-example";

        $assumeRoleCredentials = new AssumeRoleCredentialProvider([
            'client' => new StsClient([
                'region' => 'ap-south-1',
                'version' => 'latest',
                'credentials' => $profile
            ]),
            'assume_role_params' => [
                'RoleArn' => $ARN,
                'RoleSessionName' => $sessionName,
            ],
        ]);

        $provider = CredentialProvider::memoize($assumeRoleCredentials);


        $s3Client = new S3Client([
            'region'      => 'ap-south-1',
            'version'     => 'latest',
            'credentials' => $provider
        ]);

        //$fs = Storage::disk('s3');
        //$client = $fs->getClient();
        /*echo $downloadUrl = $client->getObjectUrl('covenantdev', $filename, '+5 minutes', array(
            'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
        ));die;*/
        $result = $s3Client->getObject(array(
            'Bucket' => 'covenantdev',
            'Key'    => $filename,
            'SaveAs' => $filename,
        ));


        $filepath = public_path();
        $fullpath = public_path($filename);
        if (file_exists($filepath)) {
            //return response()->download($fullpath);
            $mimeType = File::mimeType($fullpath);
            $headers = array('Content-Type'=> $mimeType,'Content-Disposition' => 'attachment; filename="'. $filename .'"',);
            //header("Content-Length: ".filesize( $fullpath ) );
            return response()->download($fullpath, $filename, $headers)->deleteFileAfterSend(true);
            //echo file_get_contents( $fullpath );
            //return \Response::make($filepath, 200, $headers);
        }
        //$data = file_get_contents('s3://covenantdev/1657875510_plastic.pdf');

        /*header('Content-type: ' . $result['ContentType']);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-length:' . $result['ContentLength']);

        echo $result['Body'];*/
        //print_r($result['Body']);die;
        //$data = file_get_contents('s3://covenantdev/'.$filename);
        //print_r($data);die;
        //echo Storage::disk('s3')->exists($filename);die;
        //return $json = json_decode(file_get_contents($url), true);
        //return response()->fileFromStorage('s3', $url, $filename);
        //return Storage::disk('s3')->download($filename,'',$headers);
        //return $temp_url = Storage::disk('s3')->temporaryUrl($filename,now()->addMinutes(5));
        //$content = Storage::disk('s3')->download($url);
        //return Download::make(Storage::disk('s3')->get($url), 200, $headers);
        //print_r($content);die;
        //return $result['Body']->getContents();
        /*return $data = file_get_contents(array(
            'Bucket' => 'covenantdev',
            'Key'    => $filename,
        ));*/
        //print_r(Storage::disk('s3')->get($url));die;
        //return \Response::make(Storage::disk('s3')->get($url), 200, $headers);
    }

    public function listOverview(Request $request) 
    {
        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 =>'cname',
            1 =>'id',
            2 =>'clcode',
            3 =>'isinValues',
            4=> 'cname',
            5=> 'secured',
            6=> 'inconsistencyTreatment',
            7=> 'complianceStatus',
            8=> 'name',
            9=>'actions',
        );
        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');

        $user_object = \Auth::user();
        //print_r($user_object->role->role);die;
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $organization_id = $user_object->organization_id;
        $viewOnly = 0;
        $isApprover = 0;
        $query = DB::table('compliances');
        $query->join('clients', 'compliances.clientReference', '=', 'clients.id');
        $query->join('users', 'compliances.userId', '=', 'users.id');
        if($user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.CCU_MAKER') || $user_role == config('global.roles.CCU_CHECKER')) {
            $viewOnly = 1;
            $key = 'compliances.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.CSOG_MAKER')) {
            $key = 'compliances.userId';
            $value = $current_user;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.CSOG_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
            $role_id = DB::table('roles')->where('role', config('global.roles.CSOG_MAKER'))->first();
            $key = 'compliances.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
            $query->where('users.role_id',$role_id->id);
        }
        else if($user_role == config('global.roles.SUPER_ADMIN')) {
            $viewOnly = 1;
        }
        if(!empty($request->input('search.value')))
        {
            $query->where('compliances.clcode','LIKE','%'.$request->input('search.value').'%');
            $query->orWhere('compliances.secured','LIKE','%'.$request->input('search.value').'%');
            $query->orWhere('compliances.inconsistencyTreatment','LIKE','%'.$request->input('search.value').'%');
            $query->orWhere('clients.name','LIKE','%'.$request->input('search.value').'%');
            $query->orWhere('users.name','LIKE','%'.$request->input('search.value').'%');
            $query->orWhere('compliances.complianceStatus','LIKE','%'.$request->input('search.value').'%');
        }
        if($order_val == 'cname')
            $query->orderByRaw('compliances.updated_at DESC');
        else
            $query->orderBy($order_val,$dir_val);
        //print_r($query->toSql());die;
        $post_count = $query->count();
        $query->offset($start_val);
        $query->limit($limit_val);
        $compliances = $query->get(['clients.name as cname','compliances.id','compliances.clcode','compliances.secured','compliances.inconsistencyTreatment','compliances.complianceStatus','users.name']);

        /*$compliances = DB::table('compliances')
            ->join('clients', 'compliances.clientReference', '=', 'clients.id')
            ->where($key,$value)
            ->get(['clients.name','compliances.id','compliances.clcode','compliances.secured','compliances.inconsistencyTreatment']);*/

        /*$compliances = Compliancetool::where('userId', $current_user)
         ->pluck('clcode', 'clientReference', 'secured','inconsistencyTreatment')
         ->all();*/

        foreach ($compliances as $key=>$compliance) {
            $actions = '';
            $isins = DB::table('compliances_isin')
                    ->select(DB::raw("GROUP_CONCAT(isin SEPARATOR ', ') as isin"))
                    ->groupBy('complianceId')
                    ->where('complianceId',$compliance->id)
                    ->get();

            if(count($isins) > 0)
                $compliances[$key]->isinValues = $isins[0]->isin;

            if($viewOnly != 1 && ($compliance->complianceStatus != 'In Progress' && $compliance->complianceStatus != 'Closed')) {
                $actions .= "<button class='edit-placeholder' aria-label='Edit' dusk='3-edit-button'><img src='/img/edit_file.png' title='Edit' /></button>"; 
            }

            $actions .= "<button class='view-placeholder'><img src='/img/view_file.png' title='View' /></button>";

            if($viewOnly != 1 && ($compliance->complianceStatus != 'In Progress' && $compliance->complianceStatus != 'Closed')) {
                $actions .= "<button class='add-placeholder'><img src='/img/add_file.png' title='Add Covenants' /></button>"; 
            }

            $actions .= "<button class='attached-placeholder'><img src='/img/view-covenant.png' title='View Attached Covenants' /></button>";

            if($isApprover == 1 && $compliance->complianceStatus == 'In Progress') {
                $actions .= "<button class='close-placeholder'><img src='/img/closed.png' title='Close Compliance' /></button>"; 
            }
            $compliances[$key]->actions = $actions;
        }

        $totalDataRecord = $post_count;
        $totalFilteredRecord = $totalDataRecord;
        $draw_val = $request->input('draw');
        $get_json_data = array(
        "draw"            => intval($draw_val),
        "recordsTotal"    => intval($totalDataRecord),
        "recordsFiltered" => intval($totalFilteredRecord),
        "data"            => $compliances,
        "viewOnly" => $viewOnly,
        "isApprover" => $isApprover,
        );
         
        echo json_encode($get_json_data);

        //$result['viewOnly'] = $viewOnly;
        //$result['isApprover'] = $isApprover;
        //$result['compliances'] = $compliances;
        //echo json_encode($result); die;

    }

    public function view(Request $request) 
    {
        $id = $request->input('id');
        try {
                $compliance = DB::table('compliances')
                ->select('compliances.id as complianceId','compliances.*',DB::raw('DATE_FORMAT(compliances.startDate, "%d-%m-%Y") as startDate'),DB::raw('DATE_FORMAT(compliances.endDate, "%d-%m-%Y") as endDate'),'clients.*')
                ->join('clients', 'compliances.clientReference', '=', 'clients.id')
                ->where('compliances.id',$id)
                ->get()
                ->first();

                $isins = DB::table('compliances_isin')
                        ->select(DB::raw("GROUP_CONCAT(isin SEPARATOR ', ') as isin"))
                        ->groupBy('complianceId')
                        ->where('complianceId',$id)
                        ->get();

                if(count($isins) > 0) {
                    $compliance->isinValues = $isins[0]->isin;
                }

                $filenames = json_decode($compliance->documentNames);
                
                $compliance->documentNames = $filenames;

        } catch (\Exception $e) {
            $compliance =  response()->json(['message'=>'compliance not found!'], 404);
        }

        $result['status'] = 'success';
        $result['compliance'] = $compliance;

        return json_encode($result);die;
    }
    public function save(Request $request) 
    {
        //print_r($request->all());die;
        $validated = $request->validate([
            'clcode' => 'required',
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d|after:startDate',
            'priority' => 'required',
            'secured' => 'required',
            'inconsistencyTreatment' => 'required',
            'clientReference' => 'required',
            'mailCC' => 'emails',
            'files.0' => 'required|file'
        ],
        [
            'files.0.*' => 'Document must be uploaded.'
        ]);

        //die;

        $current_user = \Auth::user()->id;
        $organization_id = \Auth::user()->organization_id;
        $docnames= [];
        $fileUris = array();

        foreach($request->files as $file) {
            foreach($file as $upload) {
                
                $name = time().'_'.$upload->getClientOriginalName();
                $path = Storage::disk('s3')->put($name, file_get_contents($upload));
                $path = Storage::disk('s3')->url($path);

                array_push($fileUris, $name);
            }
        }

        $docnames_json = json_encode($fileUris);
        $compliancetool = new Compliancetool([
            'clcode' => $request->clcode,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'priority' => $request->priority,
            'secured' => $request->secured,
            'inconsistencyTreatment' => $request->inconsistencyTreatment,
            'clientReference' => $request->clientReference,
            'mailCC' => $request->mailCC,
            'documentNames' => $docnames_json,
            'complianceStatus' => 'Draft',
            'userId' => $current_user,
            'organization_id' => $organization_id,
        ]);

        $compliancetool->save();

        $complianceId = $compliancetool->id;

        if($complianceId && $request->isin) {
            foreach ($request->isin as $key => $value) {
                DB::table('compliances_isin')->insert([
                    'isin' => $value,
                    'complianceId' => $complianceId
                ]);
            }
        }

        $result['complianceId'] = $complianceId;
        $result['status'] = 'success';

        echo json_encode($result);die;
    }

    public function update(Request $request) 
    {
         //print_r($request->all());die;
        $validated = $request->validate([
            'clcode' => 'required',
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d|after:startDate',
            'priority' => 'required',
            'secured' => 'required',
            'inconsistencyTreatment' => 'required',
            'clientReference' => 'required',
            'mailCC' => 'emails'
        ]);
        $compliance_id = $request->id;
        $current_user = \Auth::user()->id;
        $organization_id = \Auth::user()->organization_id;
        $docnames= [];
        if(isset($request->newfiles)) {
            foreach($request->newfiles as $upload)
            {           
                    $name= time().'_'.$upload->getClientOriginalName();
                    $filePath = '/' . $name;
                    $docnames[] = $name;
                    $path = Storage::disk('s3')->put('/'.$name, $upload);
                    $url = Storage::disk('s3')->url($name);
                    //if (!Storage::disk('s3')->exists($name)) { 
                    if (!$url) {              
                        return response()->json('Error uploading file '.$name.' Compliance could not be created.');
                    }
            }
        }
        $oldFiles = [];
        if(!empty($oldFiles))
            $oldFiles = json_decode($request->oldfiles);
        $finalFiles = array_merge($docnames,$oldFiles);
        $docnames_json = json_encode($finalFiles);

        DB::table('compliances_isin')->where('complianceId', $request->id)->delete();
        $newisins = explode(",",$request->isin);
        foreach ($newisins as $key => $value) {
            DB::table('compliances_isin')->insert([
                'isin' => $value,
                'complianceId' => $compliance_id
            ]);
        }

        $compliance_update = Compliancetool::find($compliance_id)->update([
            'clcode' => $request->clcode,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'priority' => $request->priority,
            'secured' => $request->secured,
            'inconsistencyTreatment' => $request->inconsistencyTreatment,
            'clientReference' => $request->clientReference,
            'mailCC' => $request->mailCC,
            'documentNames' => $docnames_json,
            'userId' => $current_user,
            'organization_id' => $organization_id,
        ]);

        /*$result['complianceId'] = $complianceId;*/
        $result['status'] = 'success';

        echo json_encode($result);die;
    }

    public function close(Request $request) 
    {
         //print_r($request->all());die;
        $compliance_id = $request->id;
        $result['status'] = 'fail';
        $compliance_update = Compliancetool::find($compliance_id)->update([
            'complianceStatus' => 'Closed'
        ]);
        if($compliance_update)
            $result['status'] = 'success';

        echo json_encode($result);die;
    }

    public function fetchClients() 
    {
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $org_id = $user_object->organization_id;
                
        $query = DB::table('clients');
        
        if($user_role != 'Super Admin') {
            $key = 'organization_id';
            $value = $org_id;
            $query->where($key,$value);
        }

        $clients = $query->get();
        
        $client_data = [];
        $i = 0;
        foreach ($clients as $client) {
            $client_data[$i]['id'] = $client->id;
            $client_data[$i]['name'] = $client->name;
            $i++;
        }
        echo json_encode($client_data); die;

    }

    public function fetchClcode() 
    {
        $current_user = \Auth::user()->id;
        $org_id = DB::table('users')->where('id', $current_user)->value('organization_id');
        $clodes = ComplianceClcode::select('clcode')->distinct()->get();
        $clcode_coll = [];
        foreach ($clodes as $i=>$clode) {
            $clcode_coll[$i]['clcode'] = $clode->clcode;
        }
        echo json_encode($clcode_coll); die;

    }

    public function fetchIsins(Request $request) {
        $validated = $request->validate([
            'clcode' => 'required'
        ]);
        $clcode = $request->input('clcode');
        $isins = ComplianceClcode::select('isin')->where('clcode', $clcode)->get();
        $isin_coll = [];
        foreach ($isins as $i=>$isin) {
            $isin_coll[$i] = $isin->isin;
        }
        //print_r($isin_coll);die;
        echo json_encode($isin_coll); die;
    }

    public function fetchCovenant(Request $request) 
    {
        //print_r($request->all());die;
        $covenant_data = [];
        if(!empty($request->input('standard_covenant_id'))  && $request->input('action') == 'trigger-covenant') {
            $standard_covenants = DB::table('standard_covenants')
            ->where('id',$request->input('standard_covenant_id'))
            ->get()
            ->first();

            $covenant_data[0]['value'] = $standard_covenants->type;
            $covenant_data[0]['label'] = $standard_covenants->type;
        }
        else {
            $standard_covenants = DB::table('standard_covenants')
            ->select('type')
            ->groupBy('type')
            ->get();


            $i = 0;
            foreach ($standard_covenants as $covenant) {
                $covenant_data[$i]['value'] = $covenant->type;
                $covenant_data[$i]['label'] = $covenant->type;
                $i++;
            }
        }

        echo json_encode($covenant_data); die;

    }

    public function fetchSubtypes(Request $request) 
    {
        //print_r($request->all());die;
        $covenant_details = [];
        $child_details = [];
        if($request->input('type') !== null) 
        {
            $type = $request->input('type');
            //print_r($type);die;
             try
            {
                $subtypes = [];
                if($request->input('action') == 'add' && $request->input('id')) {
                    $existing_subtypes = DB::table('compliances_covenants')
                    ->where('complianceId',$request->input('id'))
                    ->where('isCustomCovenant',0)
                    ->where('type',$type)
                    ->get('subType');

                    foreach ($existing_subtypes as $value) {
                        $subtypes[] = $value->subType;
                    }
                } 

                if(count($subtypes) > 0) {
                    $standard_covenants = DB::table('standard_covenants')
                    ->whereNotIn('sub_type',$subtypes)
                    ->where('type',$type)
                    ->get();
                } 
                else if(!empty($request->input('standard_covenant_id'))  && $request->input('action') == 'trigger-covenant') {
                    $standard_covenants = DB::table('standard_covenants')
                    ->where('id',$request->input('standard_covenant_id'))
                    ->get();

                    $subtypes[0] = $standard_covenants[0]->sub_type;

                }
                else{
                    $standard_covenants = DB::table('standard_covenants')
                    ->where('type',$type)
                    ->get();
                }

                if(count($standard_covenants) == 0 ) {
                    /*return response()->json([
                        'success' => 'false',
                        'errors'  => '',
                    ], 200);*/
                    $covenant_details[$type] = [];
                }

                $i = 0;
                foreach ($standard_covenants as $covenant) {
                    $covenant_type = $covenant->type;
                    $covenant_details[$covenant_type][$i]['childCovenant'] = [];
                    $covenant_details[$covenant_type][$i]['covenantParameters'] = [];
                    $covenant_details[$covenant_type][$i]['id'] = $covenant->id;
                    $covenant_details[$covenant_type][$i]['type'] = $covenant->type;
                    $covenant_details[$covenant_type][$i]['subType'] = $covenant->sub_type;
                    $covenant_details[$covenant_type][$i]['description'] = $covenant->description;
                    $covenant_details[$covenant_type][$i]['frequency'] = $covenant->frequency;

                    $cov_param = json_decode($covenant->covenant_parameters, true);
                    if(is_array($cov_param)) {
                        $covenant_details[$covenant_type][$i]['covenantParameters'] = $cov_param['covenant_details'];
                    }

                    $child_cov = json_decode($covenant->child_covenant, true);
                    if(is_array($child_cov)) {
                        $covenant_details[$covenant_type][$i]['childCovenant'] = $child_cov['child_covenant'];
                    }
                    $i++;
                }
                //print_r($covenant_details);die;
                return response()->json([
                        'success' => 'true',
                        'covenant'  => $covenant_details,
                    ], 200);
                //echo json_encode($covenant_details); die;
            }
            catch(Exception $e)
            {
                return response()->json([
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ], 400);
            }

        }

    }

    public function saveCovenantData(Request $request) 
    {
        $submittedData = $request->all();
        $saveStatus = 'success';
//print_r($request->all());die;
        $reduce = function ($new = array(), $x) {
            $new[array_keys($x)[0]]=array_values($x)[0];
            return $new;
        };
        $covenantChild = [];
        $standardCovenantDetails = array_reduce($submittedData['covenantInfo'], $reduce);
//print_r($standardCovenantDetails);die;
        foreach($standardCovenantDetails as $covenantInfo) {
            foreach($covenantInfo as $key=>$value) {
                if(isset($covenantInfo[$key]['selectedCovenant']) && $covenantInfo[$key]['selectedCovenant'] == true) {
                    $covenantData = [];
                    $covenantData['complianceId'] = $submittedData['complianceId'];
                    $covenantData['type'] = $covenantInfo[$key]['type'];
                    $covenantData['subType'] = $covenantInfo[$key]['subType'];
                    $covenantData['description'] = $covenantInfo[$key]['description'];
                    if(isset($submittedData['referenceCovenantId']) && $submittedData['referenceCovenantId'] != '')
                        $covenantData['associated_covenant_id'] = $submittedData['referenceCovenantId'];
                    if(isset($covenantInfo[$key]['isCustomCovenant']) && $covenantInfo[$key]['isCustomCovenant'] == 1)
                        $covenantData['isCustomCovenant'] = 1;
                    else
                        $covenantData['isCustomCovenant'] = 0;
                    $covenantData['frequency'] = $covenantInfo[$key]['frequency'];
                    $covenantData['targetValue'] = $covenantInfo[$key]['targetValue'];
                    $covenantData['startDate'] = $covenantInfo[$key]['startDate'];
                    $covenantData['applicableMonth'] = $covenantInfo[$key]['applicableMonth'];
                    $covenantData['dueDate'] = $covenantInfo[$key]['dueDate'];
                    $covenantData['comments'] = isset($covenantInfo[$key]['comment']) ? $covenantInfo[$key]['comment'] : '';
                    $covenantParameters = [];
                    if(isset($covenantInfo[$key]['covenantParameters']) && !empty($covenantInfo[$key]['covenantParameters']))
                        $covenantParameters = $covenantInfo[$key]['covenantParameters'];

                  if($covenantData['isCustomCovenant'] == 1) {
                    foreach($covenantParameters as $paramkey=>$value) {
                        $covenantData[$value['key']] = $value['label'];
                        $covenantData['custom_value'] = $value['value'];
                    }

                    $covenantChild = $covenantInfo[$key]['childCovenant'];

                    foreach($covenantChild as $childkey=>$value) {
                        $covenantData[$value['key']] = $value['label'];
                        $covenantData['custom_child_dueDate'] = $value['value'];
                    }
                  }
                  else {
                    foreach($covenantParameters as $paramkey=>$value) {
                        $covenantData[$value['key']] = $value['value'];
                    }

                    $covenantChild = $covenantInfo[$key]['childCovenant'] ? $covenantInfo[$key]['childCovenant'] : [];


                    foreach($covenantChild as $childkey=>$value) {
                        $covenantData[$value['key']] = $value['value'];
                    }
                 }

                    $complianceCovenant = new ComplianceCovenant($covenantData);

                    $complianceCovenant->save();

                    if(!$complianceCovenant->id || $complianceCovenant->id == 0) {

                        $saveStatus = 'fail';
                    } else {
                        //$inserted_ids[] = $complianceCovenant->id;
                        $covenantData['covenant_id'] = $complianceCovenant->id;
                        $covenantDataResponse = $this->saveTimelines($covenantData,$covenantChild);
                        if($covenantDataResponse == false)
                            $saveStatus = 'fail';
                        else
                            $saveStatus = 'success';
                    }
                }
            }
        }

        $result['status'] = $saveStatus;
        $result['complianceId'] = $submittedData['complianceId'];        

        echo json_encode($result);die;
    }

    public function getComplianceCovenant($covenant_id)
    {
        /*$compliance_id = $request->id;
        $compliance = DB::table('compliances')
                    ->join('clients', 'compliances.clientReference', '=', 'clients.id')
                    ->where('compliances.id',$compliance_id)
                    ->get()
                    ->first();

        $isins = DB::table('compliances_isin')
                ->select(DB::raw("GROUP_CONCAT(isin SEPARATOR ', ') as isin"))
                ->groupBy('complianceId')
                ->where('complianceId',$compliance_id)
                ->get();

        if(count($isins) > 0) {
            $compliance->isinValues = $isins[0]->isin;
        }

        $filenames = json_decode($compliance->documentNames);
        
        $compliance->documentNames = $filenames;
        $compliance->id = $compliance_id;*/

        if($covenant_id)  {
          $covenantInfo = ComplianceCovenant::FindOrFail($covenant_id);  
        }

        $child_covenants = [];
        $custom_child = [];
        $covenant_data = [];

        $covenant_data['covenant_id'] = $covenantInfo->id;
        $covenant_data['type'] = $covenantInfo->type;
        $covenant_data['subType'] = $covenantInfo->subType;
        $covenant_data['frequency'] = $covenantInfo->frequency;
        $covenant_data['startDate'] = date("d-m-Y", strtotime($covenantInfo->startDate));
        $covenant_data['dueDate'] = date("d-m-Y", strtotime($covenantInfo->dueDate));
        $covenant_data['applicableMonth'] = $covenantInfo->applicableMonth;
        $covenant_data['instances'] = $this->getParentInstancesDB($covenant_data['covenant_id']);
        $covenant_data['child'] = $this->getChildInstancesDB($covenant_data['covenant_id']);
        $covenant_data['reminder']['before'] = $covenant_data['instances'][0]['reminderBefore'];
        $covenant_data['reminder']['interval'] = $covenant_data['instances'][0]['reminderInterval'];
        /*$covenant_data['instances'] = $this->getInstances($covenantInfo->frequency,$covenantInfo->startDate,$covenantInfo->dueDate,$covenantInfo->applicableMonth);
        $covenant_data['reminder']['before'] = $covenant_data['instances'][0]->reminderBefore;
        $covenant_data['reminder']['interval'] = $covenant_data['instances'][0]->reminderInterval;
        if($covenantInfo->isCustomCovenant == 1) {
            $custom_child['label'] = $covenantInfo->custom_child;
            $custom_child['key'] = 'custom_child';
            $custom_child['value'] = $covenantInfo->custom_child_dueDate;
            if($custom_child['value'] != '') {
                $ts1 = strtotime('-1 months',strtotime($custom_child['value']));
                $custom_child['startDate'] = date('Y-m-d',$ts1);
                $custom_child['instanceNo'] = 1;
                $custom_child['activateBefore'] = 30;
                $custom_child['dueDate'] = $custom_child['value'];
                $applicableMonth = date('F', strtotime($custom_child['value']));
                $custom_child['applicableMonth'] = $applicableMonth;
                $custom_child['status'] = 'Not Started';
            }
            $covenant_data['child'][] = $custom_child;
        }
        else{

            $covenant_guide = DB::table('standard_covenants')
            ->where('sub_type',$covenantInfo->subType)
            ->get(['child_covenant'])
            ->first();

            if(isset($covenant_guide->child_covenant)) {
                $child_cov = json_decode($covenant_guide->child_covenant, true);
                if(isset($child_cov['child_covenant']) && is_array($child_cov['child_covenant']) && count($child_cov['child_covenant']) > 0) {
                    foreach ($child_cov['child_covenant'] as $value) {
                        $child_covenants['label'] = $value['label'];
                        $child_covenants['key'] = $value['key'];
                        $childKey = $value['key'];
                        $child_covenants['value'] = $covenantInfo->$childKey;
                        $ts1 = strtotime('-1 months',strtotime($child_covenants['value']));
                        $child_covenants['startDate'] = date('Y-m-d',$ts1);
                        $child_covenants['endDate'] = $child_covenants['value'];
                        $child_covenants['instanceNo'] = 1;
                        $child_covenants['activateBefore'] = 30;
                        $child_covenants['dueDate'] = $child_covenants['value'];
                        $applicableMonth = date('F', strtotime($child_covenants['value']));
                        $child_covenants['applicableMonth'] = $applicableMonth;
                        $child_covenants['status'] = 'Not Started';
                        $covenant_data['child'][] = $child_covenants;
                    }
                }
            }
        }*/
        
        return $covenant_data;
        //$compliance->covenantData = $covenant_data;
        ///echo json_encode($compliance); die;
    }

    public function getParentInstancesDB($covenant_id) {
        $selectedRows = ComplianceInstance::where('covenantId', $covenant_id)->where('is_child', 0)->orderBy('instanceNo')->get();
        return json_decode($selectedRows,true);
    }

    public function getChildInstancesDB($covenant_id) {
        $selectedRows = ComplianceInstance::where('covenantId', $covenant_id)->where('is_child', 1)->orderBy('instanceNo')->get();
        return json_decode($selectedRows,true);
    }

    public function getInstances($frequency,$startDate,$dueDate,$applicableMonth,$activateBefore=30) {
       //$data = $request->input('getData');
       $startDate = $startDate;
       $endDate = $dueDate;
       $frequency = $frequency;
       $applicableMonth = $applicableMonth;
       $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
       $instances = [];
       $activateBefore = $activateBefore ? $activateBefore : 30;
       switch($frequency) {
        case('Quarterly'):
            $ts1 = strtotime($startDate);
            $ts2 = strtotime($endDate);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

            $no_of_instances =  $diff/3;
            
            for($i=1;$i<=$no_of_instances;$i++) {
                $instances[$i]['instanceNo'] = $i;
                $dueDate = strtotime('+3 months', $ts1);
                $instances[$i]['dueDate'] = date('Y-m-d',$dueDate);
                $instances[$i]['applicableMonth'] = $applicableMonth;
                $instances[$i]['activateBefore'] = $activateBefore;
                $actb = strtotime('-'.$instances[$i]['activateBefore'].' days',$dueDate);
                $actb = $this->removeWeekendDates($actb);
                $activateDate = date('Y-m-d',$actb);
                $instances[$i]['activateDate'] = $activateDate;
                $applicableMonth_index = array_keys($months, $applicableMonth);
                if(isset($applicableMonth_index[0])) {
                    $applicableMonth_index = $applicableMonth_index[0] + 1;
                    $applicableMonth_date = $year1.'-'.$applicableMonth_index.'-'.'01';
                    $applicableMonth_calc = strtotime('+3 months',strtotime($applicableMonth_date));
                    $next_applicableMonth = date('F', $applicableMonth_calc);
                    $applicableMonth = $next_applicableMonth;
                }  
                $ts1 = $dueDate;
            }
        break;

        case('Half Yearly'):
            $ts1 = strtotime($startDate);
            $ts2 = strtotime($endDate);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

            $no_of_instances =  $diff/6;
            
            for($i=1;$i<=$no_of_instances;$i++) {
                $instances[$i]['instanceNo'] = $i;
                $dueDate = strtotime('+6 months', $ts1);
                $instances[$i]['dueDate'] = date('Y-m-d',$dueDate);
                $instances[$i]['applicableMonth'] = $applicableMonth;
                $instances[$i]['activateBefore'] = $activateBefore;
                $actb = strtotime('-'.$instances[$i]['activateBefore'].' days',$dueDate);
                $actb = $this->removeWeekendDates($actb);
                $activateDate = date('Y-m-d',$actb);
                $instances[$i]['activateDate'] = $activateDate;
                $applicableMonth_index = array_keys($months, $applicableMonth);
                if(isset($applicableMonth_index[0])) {
                    $applicableMonth_index = $applicableMonth_index[0] + 1;
                    $applicableMonth_date = $year1.'-'.$applicableMonth_index.'-'.'01';
                    $applicableMonth_calc = strtotime('+6 months',strtotime($applicableMonth_date));
                    $next_applicableMonth = date('F', $applicableMonth_calc);
                    $applicableMonth = $next_applicableMonth;
                }  
                $ts1 = $dueDate;
            }
        break;

        case('Monthly'):
            $ts1 = strtotime($startDate);
            $ts2 = strtotime($endDate);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

            $no_of_instances =  $diff/1;
            
            for($i=1;$i<=$no_of_instances;$i++) {
                $instances[$i]['instanceNo'] = $i;
                $dueDate = strtotime('+1 months', $ts1);
                $instances[$i]['dueDate'] = date('Y-m-d',$dueDate);
                $instances[$i]['applicableMonth'] = $applicableMonth;
                $instances[$i]['activateBefore'] = $activateBefore;
                $actb = strtotime('-'.$instances[$i]['activateBefore'].' days',$dueDate);
                $actb = $this->removeWeekendDates($actb);
                $activateDate = date('Y-m-d',$actb);
                $instances[$i]['activateDate'] = $activateDate;
                $applicableMonth_index = array_keys($months, $applicableMonth);
                if(isset($applicableMonth_index[0])) {
                    $applicableMonth_index = $applicableMonth_index[0] + 1;
                    $applicableMonth_date = $year1.'-'.$applicableMonth_index.'-'.'01';
                    $applicableMonth_calc = strtotime('+1 months',strtotime($applicableMonth_date));
                    $next_applicableMonth = date('F', $applicableMonth_calc);
                    $applicableMonth = $next_applicableMonth;
                }  
                $ts1 = $dueDate;
            }
        break;

        case('Annually'):
            $ts1 = strtotime($startDate);
            $ts2 = strtotime($endDate);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

            $no_of_instances =  $diff/12;
            
            for($i=1;$i<=$no_of_instances;$i++) {
                $instances[$i]['instanceNo'] = $i;
                $dueDate = strtotime('+12 months', $ts1);
                $instances[$i]['dueDate'] = date('Y-m-d',$dueDate);
                $instances[$i]['applicableMonth'] = $applicableMonth;
                $instances[$i]['activateBefore'] = $activateBefore;
                $actb = strtotime('-'.$instances[$i]['activateBefore'].' days',$dueDate);
                $actb = $this->removeWeekendDates($actb);
                $activateDate = date('Y-m-d',$actb);
                $instances[$i]['activateDate'] = $activateDate;
                $applicableMonth_index = array_keys($months, $applicableMonth);
                if(isset($applicableMonth_index[0])) {
                    $applicableMonth_index = $applicableMonth_index[0] + 1;
                    $applicableMonth_date = $year1.'-'.$applicableMonth_index.'-'.'01';
                    $applicableMonth_calc = strtotime('+12 months',strtotime($applicableMonth_date));
                    $next_applicableMonth = date('F', $applicableMonth_calc);
                    $applicableMonth = $next_applicableMonth;
                }  
                $ts1 = $dueDate;
            }
        break;

        case('One Time'):
        case('One time'):
        case('Event Based'):
            $no_of_instances =  1;    
            for($i=0;$i<$no_of_instances;$i++) {
                $instances[$i]['instanceNo'] = $i+1;
                $dueDate = strtotime($dueDate);
                $instances[$i]['dueDate'] = date('Y-m-d',$dueDate);
                $instances[$i]['activateBefore'] = $activateBefore;
                $ts1 = strtotime('-'.$activateBefore.' days',$dueDate);
                $instances[$i]['startDate'] = date('Y-m-d',$ts1);
                $applicableMonth = date('F', $dueDate);
                $instances[$i]['applicableMonth'] = $applicableMonth;
                $actb = strtotime('-'.$instances[$i]['activateBefore'].' days',$dueDate);
                $actb = $this->removeWeekendDates($actb);
                $activateDate = date('Y-m-d',$actb);
                $instances[$i]['activateDate'] = $activateDate;
            }
        break;

        case('Custom'):
            $no_of_instances =  1;    
            for($i=0;$i<$no_of_instances;$i++) {
                $instances[$i]['instanceNo'] = $i+1;
                $dueDate = strtotime($dueDate);
                $instances[$i]['dueDate'] = date('Y-m-d',$dueDate);
                $instances[$i]['activateBefore'] = $activateBefore;
                $ts1 = strtotime('-'.$activateBefore.' days',$dueDate);
                $instances[$i]['startDate'] = date('Y-m-d',$ts1);
                $applicableMonth = date('F', $dueDate);
                $instances[$i]['applicableMonth'] = $applicableMonth;
                $actb = strtotime('-'.$instances[$i]['activateBefore'].' days',$dueDate);
                $actb = $this->removeWeekendDates($actb);
                $activateDate = date('Y-m-d',$actb);
                $instances[$i]['activateDate'] = $activateDate;
            }
        break;

        default:
                $msg = 'Something went wrong.';
       }

       return $instances;

       //echo json_encode($instances); die;
    }

    public function saveTimelines($covenantInfo,$childData=[]) {

        //print_r($childData);die;
        try {
        $covenantInfo['instances'] = $this->getInstances($covenantInfo['frequency'],$covenantInfo['startDate'],$covenantInfo['dueDate'],$covenantInfo['applicableMonth']);
        $status = 'fail';
        $default_reminder_before = 15;
        $default_reminder_interval = 5;
        $reminder_interval = (isset($covenantInfo['reminder']['interval'])) ? $covenantInfo['reminder']['interval'] : $default_reminder_interval;
        $reminder_before = (isset($covenantInfo['reminder']['before'])) ? $covenantInfo['reminder']['before'] : $default_reminder_before;
        //$activateBefore = (isset($covenantInfo['activateBefore'])) ? $covenantInfo['activateBefore'] : 30;
        $instanceNo = 1;
        foreach($covenantInfo['instances'] as $key=>$instances) {
            $ts1 = strtotime('-'.$instances['activateBefore'].' days',strtotime($instances['dueDate']));
            $activateDate = date('Y-m-d',$ts1);
            $complianceInstance = new ComplianceInstance([
                'complianceId' => $covenantInfo['complianceId'],
                'covenantId' => $covenantInfo['covenant_id'],
                'instanceNo' => $instanceNo,
                'activateDate' => $instances['activateDate'],
                'dueDate' => $instances['dueDate'],
                'is_child' => false,
                'applicableMonth' => $instances['applicableMonth'],
                'reminderBefore' => $reminder_before,
                'reminderInterval' => $reminder_interval,
            ]);
            $complianceInstance->status = 'Not Started';

            $complianceInstance->save();

            $complianceInstanceId = $complianceInstance->id;

            if($complianceInstanceId && $complianceInstanceId > 0) {
                $status = 'success';
                $noOfReminders = 0;
                $reminderDateTsp = strtotime('-'.$reminder_before.' days',strtotime($instances['dueDate']));
                $noOfReminders = intdiv($reminder_before, $reminder_interval);
                for($i=0;$i<$noOfReminders;$i++) {
                    $reminderDate = date('Y-m-d',$reminderDateTsp);
                    $complianceReminder = new ComplianceReminder([
                        'instance_id' => $complianceInstanceId,
                        'reminder_date' => $reminderDate,
                        'email_sent' => 0,
                        'status' => 0,
                    ]);

                    $complianceReminder->save();
                    
                    $reminderDateTsp = strtotime('+'.$reminder_interval.' days',$reminderDateTsp);

                }
            }
            $instanceNo++;
        }

            if(isset($childData) && count($childData) > 0) {
                foreach($childData as $child) {
                    /*$ts1 = strtotime('-'.$activateBefore.' days',strtotime($child['value']));
                    $activateDate = date('Y-m-d',$ts1);
                    $applicableMonth = date('F', strtotime($child['value']));*/
                    $child['instances'] = $this->getInstances('One time','',$child['value'],'');
                    $instanceNo = 1;
                    foreach ($child['instances'] as $value) {
                        $complianceInstance = new ComplianceInstance([
                            'complianceId' => $covenantInfo['complianceId'],
                            'covenantId' => $covenantInfo['covenant_id'],
                            'instanceNo' => $instanceNo,
                            'activateDate' => $value['activateDate'],
                            'dueDate' => $value['dueDate'],
                            'is_child' => true,
                            'child_label' => $child['label'],
                            'applicableMonth' => $value['applicableMonth'],
                            'reminderBefore' => $reminder_before,
                            'reminderInterval' => $reminder_interval,
                        ]);
                        $complianceInstance->status = 'Not Started';

                        $complianceInstance->save();

                        $complianceInstanceId = $complianceInstance->id;

                        if(!$complianceInstanceId || $complianceInstanceId < 0) {
                            $status = 'fail';
                        }
                        else {
                            $status = 'success';
                            $noOfReminders = 0;
                            $reminderDateTsp = strtotime('-'.$reminder_before.' days',strtotime($child['value']));
                            $noOfReminders = intdiv($reminder_before, $reminder_interval);
                            for($i=0;$i<$noOfReminders;$i++) {
                                $reminderDate = date('Y-m-d',$reminderDateTsp);
                                $complianceReminder = new ComplianceReminder([
                                    'instance_id' => $complianceInstanceId,
                                    'reminder_date' => $reminderDate,
                                    'email_sent' => 0,
                                    'status' => 0,
                                ]);

                                $complianceReminder->save();
                                
                                $reminderDateTsp = strtotime('+'.$reminder_interval.' days',$reminderDateTsp);

                            }
                        }
                        $instanceNo++;
                    }
                }
            }
            return true;
        }
        catch(Exception $e)
            {
                return false;
                /*return response()->json([
                    'status' => 'fail',
                    'errors'  => $e->getMessage(),
                ], 400);*/
            }
        

        return true;

    }

    public function deleteInstanceReminders($covenant_id) {
        if($covenant_id) {
            $deletedRows = ComplianceInstance::where('covenantId', $covenant_id)->delete();
        }
    }

    public function addReminder($instanceId,$dueDate,$reminderBefore,$reminderInterval) {
        $noOfReminders = 0;
        $reminderDateTsp = strtotime('-'.$reminderBefore.' days',strtotime($dueDate));
        $noOfReminders = intdiv($reminderBefore, $reminderInterval);
        for($i=0;$i<$noOfReminders;$i++) {
            $reminderDate = date('Y-m-d',$reminderDateTsp);
            $complianceReminder = new ComplianceReminder([
                'instance_id' => $instanceId,
                'reminder_date' => $reminderDate,
                'email_sent' => 0,
                'status' => 0,
            ]);

            $complianceReminder->save();
            
            $reminderDateTsp = strtotime('+'.$reminderInterval.' days',$reminderDateTsp);

        }
    }

    public function activityLogs() {

        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $organization_id = $user_object->organization_id;
        $query = DB::table('activity_log');
        $query->join('users', 'activity_log.causer_id', '=', 'users.id');
        if($user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.CCU_CHECKER')  || $user_role == config('global.roles.CSOG_CHECKER')) {
            $user_ids = DB::table('users')
                    ->select('id')
                    ->where('organization_id',$organization_id)
                    ->get();
            $user_arr = [];
            if(count($user_ids) > 0) {
                foreach ($user_ids as $id) {
                    $user_arr[] = $id->id;
                }
            }
            //print_r($user_arr);die;
            $query->whereIn('users.id',$user_arr);
        }
        else if($user_role == config('global.roles.CSOG_MAKER') || $user_role == config('global.roles.CCU_MAKER')){
            return false;
        }

        $logs = $query->get(['activity_log.*','users.name']);
        $result['status'] = 'success';
        $result['logs'] = $logs;

        return inertia('ActivityLog',[
            'compliances' => $logs,
        ]);
    }

    public function viewLog(Request $request) {
        $id = $request->id;
        $properties = CustomActivityLog::where('id', $id)->get('properties')->first();
        $prop_arr = json_decode($properties['properties'],true);
        $final_arr = [];
        foreach($prop_arr['attributes'] as $key => $value) {
            $final_arr[$key]['new'] = $value;
            $final_arr[$key]['old'] = isset($prop_arr['old']) ? $prop_arr['old'][$key] : '';
        }
        return $final_arr;
    }

    public function bulkUpload(Request $request) {
        //print_r(json_decode($request->row,true));die;
        if(!empty($request->csvfile)) {
            $filename = $request->csvfile;
            $delimiter = ",";
            $header = null;
            $regrp = array();
            $inserted_data = [];
            if (($handle = fopen($filename, 'r')) !== false)
            {
                $i = 1;
                $result = [];
                while (($row = fgetcsv($handle, 5000, $delimiter)) !== false)
                {
                    if (!$header)
                        $header = $row;
                    else {
                        $regrp = array_combine($header, $row);
                        if(!array_key_exists($row[0],$result)) {
                            $result[$row[0]] = [];
                        }
                        array_push($result[$row[0]],$regrp);
                    }

                    $i++;
                }

                fclose($handle);
            }
            $saveStatus = 'success';
        }
        $compliance_cnt = 0;

        foreach($result as $grpKey) {
            $emp_code = $grpKey[0]['user'];
            $id = '';
            $user_info = [];
            if($emp_code) {
                $user_info = DB::table('users')
                    ->select('id','organization_id')
                    ->where('employee_code',$emp_code)
                    ->get();
            }

            if($grpKey[0]['clientReference']) {
                $client_info = DB::table('clients')
                    ->select('id')
                    ->where('name',$grpKey[0]['clientReference'])
                    ->get();
            }
            if(isset($user_info[0]) && $user_info[0]->id != '') {
                $inconsistencyTreatment = $grpKey[0]['inconsistencyTreatment'] ? $grpKey[0]['inconsistencyTreatment'] : 'NA';
                $complianceTool = new Compliancetool([
                    'clcode' => $grpKey[0]['clcode'],
                    'startDate' => date("Y-m-d", strtotime($grpKey[0]['startDate'])),
                    'endDate' => date("Y-m-d", strtotime($grpKey[0]['endDate'])),
                    'priority' => $grpKey[0]['priority'],
                    'secured' => $grpKey[0]['secured'],
                    'inconsistencyTreatment' => $inconsistencyTreatment,
                    //'clientReference' => json_decode($request->clientReference)[$i],
                    'clientReference' => $client_info[0]->id,
                    'mailCC' => $grpKey[0]['mailCC'],
                    'documentNames' => '',
                    'userId' => $user_info[0]->id,
                    'organization_id' => $user_info[0]->organization_id,
                ]);

                $complianceTool->save();
                $id = $complianceTool->id;
                if($id) {
                    $compliance_cnt++;
                    $inserted_data[$compliance_cnt]['compliance_id'] = $id;
                }
                $covenant_cnt = 0;
                foreach($grpKey as $data) {

                    if($user_info[0]->id != '') {
                        if($id != '') {
                            $isin = $data['isin'];
                            $isin_set = explode(",", $isin);

                            for ( $j = 0 ; $j < count($isin_set) ; $j++ ) {
                                $complianceIsin = new ComplianceIsin([
                                    'complianceId' => $id,
                                    'isin' => $isin_set[$j],
                                ]);
                                $complianceIsin->save();
                            }
                            $isCustomCovenant = $data['isCustomCovenant'];
                            $covenantStartDate = $data['covenantStartDate'];
                            $covenantEndDate = $data['covenantEndDate'];
                            $applicableMonth = $data['applicableMonth'];
                            $comments = $data['comments'];

                            $covenantData['complianceId'] = $id;
                            $covenantData['type'] = $data['type'];
                            $covenantData['subType'] = $data['subType'];
                            $covenantData['description'] = $data['description'];
                            if(isset($isCustomCovenant) && $isCustomCovenant == 1)
                                $covenantData['isCustomCovenant'] = 1;
                            else
                                $covenantData['isCustomCovenant'] = 0;
                            $covenantData['frequency'] = $data['frequency'];
                            $covenantData['targetValue'] = $data['targetValue'];
                            $covenantData['startDate'] = date("Y-m-d", strtotime($covenantStartDate));
                            $covenantData['applicableMonth'] = $applicableMonth;
                            $covenantData['dueDate'] = date("Y-m-d", strtotime($covenantEndDate));
                            $basicCovenantdata = $covenantData;
                            $parameters = $this->getCovenantParameters();
                            foreach ($parameters as $value) {
                                if(isset($data[$value]) && $data[$value] != '') {
                                    if($value == 'custom_child_dueDate' || $value == 'period_for_chg_from_security_creation' || $value == 'period_for_cersai_from_security_creation' || $value == 'period_of_replenishment_after_shortfall' || $value == 'period_for_renewal_before_expiry' || $value == 'date_of_invocation') {

                                            $covenantData[$value] = date("Y-m-d", strtotime($data[$value]));
                                    }
                                    else {
                                        $covenantData[$value] = $data[$value];
                                    }
                                }
                            }

                            $complianceCovenant = new ComplianceCovenant($covenantData);

                            $complianceCovenant->save();
                            $covenant_id = $complianceCovenant->id;
                            
                            if($covenant_id != '') {
                                $covenant_cnt++;
                                $inserted_data[$compliance_cnt]['covenant_id'][$covenant_cnt] = $covenant_id;
                                $covenantData = [];
                                $basicCovenantdata['covenant_id'] = $covenant_id;

                                $covenantDataResponse = $this->saveTimelines($basicCovenantdata,[]);
                                if($covenantDataResponse == false) {
                                    $saveStatus = 'fail';
                                    //return false;
                                }
                                else {
                                    $saveStatus = 'success';
                                    //return true;
                                }
                            }
                        }
                        else
                         $saveStatus = 'fail';
                    }
                    else
                     $saveStatus = 'fail';
                }                                  
            }
            else {
             $saveStatus = 'fail';
            }
        }
        $summary['total_count'] = count($inserted_data);
        $summary['data_uploaded'] = $inserted_data; 
        $summary['save_status'] = $saveStatus; 
        echo json_encode($summary);
    }

    public function removeWeekendDates($timestamp) {
        $weekDay = date('w', $timestamp);
        if($weekDay == 0) {
            return strtotime('-2 days',$timestamp);
        }
        if($weekDay == 6) {
            return strtotime('-1 days',$timestamp);
        }
        return $timestamp;
    }

    public function getCovenantParameters() {
        return array('maintained_as','manner_of_creation','account_number','additional_details','is_manner_invoked','amount','period_for_renewal_before_expiry','period_of_replenishment_after_shortfall','rating_symbol','suffix','bank_account_number','custom_parameter','custom_value','custom_child_dueDate','custom_child','period_for_chg_from_security_creation','period_for_cersai_from_security_creation','time_bucket','change_in_kmp','change_in_boardOfDirectors','change_in_significant_person','restriction_on_fund_raising','kind_of_fund_restriction','manner_of_restriction_revocation');
    }

}