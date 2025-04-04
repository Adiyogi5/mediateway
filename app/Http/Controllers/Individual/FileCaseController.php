<?php

namespace App\Http\Controllers\Individual;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\FileCase;
use App\Models\FileCasePayment;
use App\Models\Individual;
use App\Models\ServiceFee;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use App\Models\State;

class FileCaseController extends Controller
{
    private $razor_pay_key = '';
    private $razor_pay_secret = '';

    public function __construct()
    {
        $this->middleware('auth:individual');
        
        $settings = Setting::select('setting_name', 'filed_value')->get()->pluck('filed_value', 'setting_name')->toArray();

        $this->razor_pay_key = $settings['razorpay_key_id'];
        $this->razor_pay_secret = $settings['razorpay_secret_key'];
    }

    public function index(Request $request): View | JsonResponse
    {
        $title = 'File Cases List';

        $individual = auth('individual')->user();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        $casefilepayment = FileCasePayment::select('file_case_payments.*','file_cases.amount_in_dispute','file_cases.case_type')
        ->where('file_cases.individual_id',$individual->id)
        ->leftJoin('file_cases','file_cases.id','=','file_case_payments.file_case_id')
        ->first();

        if ($request->ajax()) {
            $data = FileCase::select('file_cases.id', 'file_cases.case_type', 'file_cases.individual_id', 'file_cases.claimant_first_name', 'file_cases.claimant_last_name', 'file_cases.claimant_mobile', 'file_cases.respondent_first_name', 'file_cases.respondent_last_name', 'file_cases.respondent_mobile', 'file_cases.status', 'file_cases.created_at','file_case_payments.transaction_id')
                ->where('file_cases.individual_id', auth()->id())
                ->where('file_cases.status', 1)
                ->leftJoin('file_case_payments', 'file_cases.id', '=', 'file_case_payments.file_case_id');
            return Datatables::of($data)
                ->editColumn('case_type', function ($row) {
                    return config('constant.case_type')[$row->case_type] ?? 'Unknown';
                })            
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {
                    if (empty($row->transaction_id) || $row->transaction_id == null || $row->payment_status == 1) {
                        $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button>';
                        $btn .= '<div class="dropdown-menu" aria-labelledby="drop">';
                        $btn .= '<a class="dropdown-item" href="' . route('individual.case.filecasepayment', $row->id) . '">Pay</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('individual.case.filecaseview.edit', $row->id) . '">Edit</a>';
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row->id . '">Delete</button>';
                        $btn .= '</div>';
                        return $btn;
                    }
                    return ''; // Hide the button if transaction_id is not empty
                })                
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status','case_type'])
                ->make(true);
        }

        return view('individual.case.filecaseview', compact('individual','title','casefilepayment'));
    }

    public function filecase(Request $request): View
    {
        $title = 'File a Case';

        $individual = auth('individual')->user();
        $individualsData = Individual::where('id',$individual->id)->first();
        $states = State::all();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        return view('individual.case.filecase', compact('individual','title','states','individualsData'));
    }

    public function registerCase(Request $request)
    {  
       
        $user = Auth::guard('individual')->user();
        
        // Validation
        $validator = Validator::make($request->all(), [
            'claimant_first_name' => 'required|max:100',
            'claimant_middle_name' => 'nullable|max:100',
            'claimant_last_name' => 'nullable|max:100',
            'claimant_mobile' => 'required|digits:10',
            'claimant_email' => 'required|email|max:100',
            'claimant_address1' => 'required',
            'claimant_address2' => 'nullable',
            'claimant_address_type' => 'required',
            'claimant_state_id' => 'required|exists:states,id',
            'claimant_city_id' => 'required|exists:cities,id',
            'claimant_pincode' => 'required',
            'respondent_first_name' => 'required|max:100',
            'respondent_middle_name' => 'nullable|max:100',
            'respondent_last_name' => 'nullable|max:100',
            'respondent_mobile' => 'required|digits:10',
            'respondent_email' => 'nullable|email|max:100',
            'respondent_address1' => 'required',
            'respondent_address2' => 'nullable',
            'respondent_address_type' => 'required',
            'respondent_state_id' => 'required|exists:states,id',
            'respondent_city_id' => 'required|exists:cities,id',
            'respondent_pincode' => 'required',
            'brief_of_case' => 'required',
            'amount_in_dispute' => 'nullable',
            'case_type' => 'required',
            'language' => 'nullable',
            'agreement_exist' => 'nullable',
            'application_form' => 'nullable|max:4096',
            'foreclosure_statement' => 'nullable|max:4096',
            'loan_agreement' => 'nullable|max:4096',
            'account_statement' => 'nullable|max:4096',
            'other_document' => 'nullable|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }

        // Initialize variables
        $uploadApplicationFormPath = null;
        $uploadForeclosureStatementPath = null;
        $uploadLoanAgreementPath = null;
        $uploadAccountStatementPath = null;
        $uploadOtherDocumentPath = null;
    
        // Handle file uploads
        if ($request->hasFile('application_form')) {
            $uploadApplicationFormPath = Helper::saveFile($request->file('application_form'), 'individuals/casefile');
        }
        if ($request->hasFile('foreclosure_statement')) {
            $uploadForeclosureStatementPath = Helper::saveFile($request->file('foreclosure_statement'), 'individuals/casefile');
        }
        if ($request->hasFile('loan_agreement')) {
            $uploadLoanAgreementPath = Helper::saveFile($request->file('loan_agreement'), 'individuals/casefile');
        }
        if ($request->hasFile('account_statement')) {
            $uploadAccountStatementPath = Helper::saveFile($request->file('account_statement'), 'individuals/casefile');
        }
        if ($request->hasFile('other_document')) {
            $uploadOtherDocumentPath = Helper::saveFile($request->file('other_document'), 'individuals/casefile');
        }
       
        // Generate a new unique file case number
        $lastCase = FileCasePayment::latest()->first();
        $lastCaseNo = $lastCase ? intval(substr($lastCase->file_case_no, 4)) : 0;
        $newCaseNumber = 'CASE' . str_pad($lastCaseNo + 1, 5, '0', STR_PAD_LEFT);
      
        // Save case data with proper file paths
        $case = FileCase::create([
            'user_type'                 => 1,
            'individual_id'             => $user->id,
            'claimant_first_name'       => $request->claimant_first_name,
            'claimant_middle_name'      => $request->claimant_middle_name,
            'claimant_last_name'        => $request->claimant_last_name,
            'claimant_mobile'           => $request->claimant_mobile,
            'claimant_email'            => $request->claimant_email,
            'claimant_address1'         => $request->claimant_address1,
            'claimant_address2'         => $request->claimant_address2,
            'claimant_address_type'     => $request->claimant_address_type,
            'claimant_state_id'         => $request->claimant_state_id,
            'claimant_city_id'          => $request->claimant_city_id,
            'claimant_pincode'          => $request->claimant_pincode,
            'respondent_first_name'     => $request->respondent_first_name,
            'respondent_middle_name'    => $request->respondent_middle_name,
            'respondent_last_name'      => $request->respondent_last_name,
            'respondent_mobile'         => $request->respondent_mobile,
            'respondent_email'          => $request->respondent_email,
            'respondent_address1'       => $request->respondent_address1,
            'respondent_address2'       => $request->respondent_address2,
            'respondent_address_type'   => $request->respondent_address_type,
            'respondent_state_id'       => $request->respondent_state_id,
            'respondent_city_id'        => $request->respondent_city_id,
            'respondent_pincode'        => $request->respondent_pincode,
            'brief_of_case'             => $request->brief_of_case,
            'amount_in_dispute'         => $request->amount_in_dispute,
            'case_type'                 => $request->case_type,
            'language'                  => $request->language,
            'agreement_exist'           => $request->agreement_exist,
            'application_form'          => $uploadApplicationFormPath,
            'foreclosure_statement'     => $uploadForeclosureStatementPath,
            'loan_agreement'            => $uploadLoanAgreementPath,
            'account_statement'         => $uploadAccountStatementPath,
            'other_document'            => $uploadOtherDocumentPath,
        ]);
       
          // Save payment data
        FileCasePayment::create([
            'file_case_id'    => $case->id,
            'file_case_no'    => $newCaseNumber,
            'name'            => $user->name,
            'mobile'          => $user->mobile,
            'email'           => $user->email,
            'message'         => 'Payment for case file ' . $newCaseNumber,
            'transaction_id'  => null, // Assuming transaction will be added later
            'payment_status'  => 0, // Payment not completed yet
            'payment_date'    => null, // Assuming current date as default
            'payment_amount'  => 0.00, // Assuming amount will be updated later
        ]);
        
        return response()->json(['success' => true, 'message' => 'Case registered successfully!']);
    }
    

    public function edit($id): View|RedirectResponse
    {
        $title = 'Edit Filed Case';
        $individual_authData = auth('individual')->user();
        
        $caseviewData   = FileCase::Find($id);
        $states = State::all();
        
        if (!$caseviewData) {
            return to_route('individual.case.filecaseview')->withError('Filed Case Not Found..!!');
        }
        return view('individual.case.edit', compact('caseviewData','title','individual_authData','states'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        // dd($request->all());
        $caseviewData   = FileCase::Find($id);
        $individual_authData = auth('individual')->user();

        if (!$caseviewData) {
            return to_route('individual.case.filecaseview')->withError('Filed Case Not Found..!!');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'claimant_first_name' => 'required|string|max:100',
            'claimant_middle_name' => 'nullable|string|max:100',
            'claimant_last_name' => 'nullable|string|max:100',
            'claimant_mobile' => 'required|digits:10',
            'claimant_email' => 'required|email|max:100',
            'claimant_address1' => 'required',
            'claimant_address2' => 'nullable',
            'claimant_address_type' => 'required',
            'claimant_state_id' => 'required|exists:states,id',
            'claimant_city_id' => 'required|exists:cities,id',
            'claimant_pincode' => 'required',
            'respondent_first_name' => 'required|string|max:100',
            'respondent_middle_name' => 'nullable|string|max:100',
            'respondent_last_name' => 'nullable|string|max:100',
            'respondent_mobile' => 'required|digits:10',
            'respondent_email' => 'nullable|email|max:100',
            'respondent_address1' => 'required',
            'respondent_address2' => 'nullable',
            'respondent_address_type' => 'required',
            'respondent_state_id' => 'required|exists:states,id',
            'respondent_city_id' => 'required|exists:cities,id',
            'respondent_pincode' => 'required',
            'brief_of_case' => 'required',
            'amount_in_dispute' => 'nullable',
            'case_type' => 'required',
            'language' => 'nullable',
            'agreement_exist' => 'nullable',
            'application_form' => 'nullable|max:4096',
            'foreclosure_statement' => 'nullable|max:4096',
            'loan_agreement' => 'nullable|max:4096',
            'account_statement' => 'nullable|max:4096',
            'other_document' => 'nullable|max:4096',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle file uploads only if new files are uploaded
        if ($request->hasFile('application_form')) {
            $uploadApplicationFormPath = Helper::saveFile($request->file('application_form'), 'individuals/casefile');
        } else {
            $uploadApplicationFormPath = $caseviewData->application_form;
        }

        if ($request->hasFile('foreclosure_statement')) {
            $uploadForeclosureStatementPath = Helper::saveFile($request->file('foreclosure_statement'), 'individuals/casefile');
        } else {
            $uploadForeclosureStatementPath = $caseviewData->application_form; 
        }

        if ($request->hasFile('loan_agreement')) {
            $uploadLoanAgreementPath = Helper::saveFile($request->file('loan_agreement'), 'individuals/casefile');
        } else {
            $uploadLoanAgreementPath = $caseviewData->application_form;
        }

        if ($request->hasFile('account_statement')) {
            $uploadAccountStatementPath = Helper::saveFile($request->file('account_statement'), 'individuals/casefile');
        } else {
            $uploadAccountStatementPath = $caseviewData->application_form; 
        }

        if ($request->hasFile('other_document')) {
            $uploadOtherDocumentPath = Helper::saveFile($request->file('other_document'), 'individuals/casefile');
        } else {
            $uploadOtherDocumentPath = $caseviewData->application_form;
        }

        // Update case data
        $caseviewData->update([
            'usertype'                  => 1,
            'individual_id'             => $individual_authData->id,
            'claimant_first_name'       => $request->claimant_first_name,
            'claimant_middle_name'      => $request->claimant_middle_name,
            'claimant_last_name'        => $request->claimant_last_name,
            'claimant_mobile'           => $request->claimant_mobile,
            'claimant_email'            => $request->claimant_email,
            'claimant_address1'         => $request->claimant_address1,
            'claimant_address2'         => $request->claimant_address2,
            'claimant_address_type'     => $request->claimant_address_type,
            'claimant_state_id'         => $request->claimant_state_id,
            'claimant_city_id'          => $request->claimant_city_id,
            'claimant_pincode'          => $request->claimant_pincode,
            'respondent_first_name'     => $request->respondent_first_name,
            'respondent_middle_name'    => $request->respondent_middle_name,
            'respondent_last_name'      => $request->respondent_last_name,
            'respondent_mobile'         => $request->respondent_mobile,
            'respondent_email'          => $request->respondent_email,
            'respondent_address1'       => $request->respondent_address1,
            'respondent_address2'       => $request->respondent_address2,
            'respondent_address_type'   => $request->respondent_address_type,
            'respondent_state_id'       => $request->respondent_state_id,
            'respondent_city_id'        => $request->respondent_city_id,
            'respondent_pincode'        => $request->respondent_pincode,
            'brief_of_case'             => $request->brief_of_case,
            'amount_in_dispute'         => $request->amount_in_dispute,
            'case_type'                 => $request->case_type,
            'language'                  => $request->language,
            'agreement_exist'           => $request->agreement_exist,
            'application_form'          => $uploadApplicationFormPath,
            'foreclosure_statement'     => $uploadForeclosureStatementPath,
            'loan_agreement'            => $uploadLoanAgreementPath,
            'account_statement'         => $uploadAccountStatementPath,
            'other_document'            => $uploadOtherDocumentPath,
        ]);

        return to_route('individual.case.filecaseview')->withSuccess('Filed Case Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new FileCase, $request->id);
    }


    // ################## Razor Pay Payment ##################
    public function filecasepayment(Request $request)
    {
        $individual = auth('individual')->user();
        $title = 'File Case Payment';

        $settings = Setting::select('setting_name', 'filed_value')->get()->pluck('filed_value', 'setting_name')->toArray();
        $razor_pay_key = $settings['razorpay_key_id'];
        $razor_pay_secret = $settings['razorpay_secret_key'];

        $casefilepayment = FileCasePayment::select('file_case_payments.*','file_cases.amount_in_dispute','file_cases.case_type')
                ->where('file_cases.individual_id',$individual->id)
                ->leftJoin('file_cases','file_cases.id','=','file_case_payments.file_case_id')
                ->first();

        if (!$casefilepayment) {
            return redirect()->route('front.home')->with('error',"File Case not found, Please try again");
        }

        $serviceFee = ServiceFee::where('status', 1)->get();
        $amount_in_dispute = (float) $casefilepayment->amount_in_dispute;

        // Find the appropriate service fee
        $selectedFee = $serviceFee->firstWhere(function ($fee) use ($amount_in_dispute) {
            return $amount_in_dispute >= $fee->ticket_size_min && $amount_in_dispute <= $fee->ticket_size_max;
        });
        
        // Assign the corresponding cost
        $case_file_amount = $selectedFee ? (float) $selectedFee->cost : 0;
    
        $razorpayOrderId = '';
        $payment_data = '';
        
        if($casefilepayment->payment_status == 1)
        {
            return view('individual.case.filecaseview', compact('title'));
        }
        elseif($case_file_amount <= 0)
        {
            $casefilepayment->payment_status = 1;
            $casefilepayment->payment_amount = 0;
            $casefilepayment->payment_date = date('Y-m-d');
            $casefilepayment->save();
        }
        else{
            $api = new Api($razor_pay_key, $razor_pay_secret);
                
            $orderData = [
                'receipt'         => "r-".time(),
                'amount'          => $case_file_amount  * 100,
                'currency'        => 'INR',
                'payment_capture' => 1 // auto capture
            ];

            $razorpayOrder = $api->order->create($orderData);

            $razorpayOrderId = $razorpayOrder['id'];
            Session::put($razorpayOrderId);
            
            $payment_data = [
                "key"               => $razor_pay_key,
                "amount"            => $case_file_amount * 100,
                "name"              => $settings['application_name'],
                "description"       => mb_strimwidth($settings['site_description'],0,200,'..'),
                "image"             => $settings['logo'],
                "prefill"           => [
                "name"              => $casefilepayment->name,
                "email"             => $casefilepayment->email,
                "contact"           => $casefilepayment->mobile,
                ],
                "notes"             => [
                "merchant_order_id" => $casefilepayment->id,
                ],
                "theme"             => [
                "color"             => "#5a9b6a "
                ],
                "razorpay_order_id"          => $razorpayOrderId,
                "order_id"          => $razorpayOrderId,
                "display_currency"  =>"INR",
                "display_amount"    => $case_file_amount
            ];
            
        }

        return view('individual.case.filecasepayment', ['casefilepayment' => $casefilepayment, 'case_file_amount' => $case_file_amount, 'payment_json' => json_encode($payment_data)], compact('title','razorpayOrderId'));
    }


    public function verify_payment(Request $request)
    {
        $individual = auth('individual')->user();

        $settings = Setting::select('setting_name', 'filed_value')->get()->pluck('filed_value', 'setting_name')->toArray();
        $razor_pay_key = $settings['razorpay_key_id'];
        $razor_pay_secret = $settings['razorpay_secret_key'];

        $success = true;

        $error = "Payment Failed";
        
        if(!empty($request->razorpay_payment_id))
        {
            $api = new Api($razor_pay_key, $razor_pay_secret);
            try
            {
                $attributes = array(
                    'razorpay_order_id' => $_POST['razorpay_order_id'],
                    'razorpay_payment_id' => $_POST['razorpay_payment_id'],
                    'razorpay_signature' => $_POST['razorpay_signature']
                );

                $api->utility->verifyPaymentSignature($attributes);
            }
            catch(SignatureVerificationError $e)
            {   
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();

                return redirect()->route('individual.case.filecasepayment')->with('error',"File Case not found, Please try again");
            }
        }

        if ($success === true)
        {
            $individual = auth('individual')->user();;
            $title = 'Booking Payment';

            $razorpay_order_id = session('razorpayOrderId');
            $payment_id = $request->razorpay_payment_id;
            $case_file_amount = (float) $settings['case_file_amount'];

            // $json = ['payment_id'=>$payment_id,'order_id'=>$razorpay_order_id];

            $casefilepayment = FileCasePayment::select('file_case_payments.*','file_cases.amount_in_dispute','file_cases.case_type')
                ->where('file_cases.individual_id',$individual->id)
                ->leftJoin('file_cases','file_cases.id','=','file_case_payments.file_case_id')
                ->first();

            // $booking->payment_json = json_encode($json);
            $casefilepayment->transaction_id = $payment_id;
            $casefilepayment->payment_status = 1;
            $casefilepayment->payment_amount = $case_file_amount;
            $casefilepayment->payment_date = date('Y-m-d');
            $casefilepayment->save();

            // Session::forget('booking_id');
            Session::forget('razorpayOrderId');

            // $request->session()->flash('success','Payment procced successfully');
            return view('individual.case.filecasepayment_success', compact('title','casefilepayment'));
        }
        else
        {
            $request->session()->flash('error','Payment failed error : '.$error);
            return redirect()->route('individual.case.filecasepayment')->with('error',"File Case not found, Please try again");
        }
    }


    public function filecasepayment_success(Request $request)
    {
        $individual = auth('individual')->user();
        $title = 'File Case Success';
        
        $casefilepayment = FileCasePayment::select('file_case_payments.*','file_cases.amount_in_dispute','file_cases.case_type')
            ->where('file_cases.individual_id',$individual->id)
            ->leftJoin('file_cases','file_cases.id','=','file_case_payments.file_case_id')
            ->first();

        if (!$casefilepayment) {
            return redirect()->route('front.home')->with('error',"File Case not found, Please try again");
        }

        return view('individual.case.filecasepayment_success', ['casefilepayment' => $casefilepayment], compact('title'));
    }
    
}