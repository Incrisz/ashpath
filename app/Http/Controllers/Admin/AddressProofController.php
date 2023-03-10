<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AddressProofsDataTable;
use App\Http\Controllers\Users\EmailController;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AddressProofsExport;
use Session, Common, Config;
use Illuminate\Http\Request;
use App\Models\{DocumentVerification, 
    EmailTemplate, 
    User
};

class AddressProofController extends Controller
{
    protected $helper;
    protected $documentVerification;
    protected $email;

    public function __construct()
    {
        $this->helper              = new Common();
        $this->addressVerification = new DocumentVerification();
        $this->email               = new EmailController();
    }

    public function index(AddressProofsDataTable $dataTable)
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'address-proofs';

        $data['documentVerificationStatus'] = $this->addressVerification->where(['verification_type' => 'address'])->select('status')->groupBy('status')->get();

        $data['from']     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']       = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status']   = isset(request()->status) ? request()->status : 'all';

        return $dataTable->render('admin.verifications.address_proofs.list', $data);
    }

    public function addressProofsCsv()
    {
        return Excel::download(new AddressProofsExport(), 'address_proofs_list_' . time() . '.xlsx');
    }

    public function addressProofsPdf()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;

        $data['addressProofs'] = $this->addressVerification->getAddressVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
        if (isset($from) && isset($to)) {
            $data['date_range'] = $from . ' To ' . $to;
        } else {
            $data['date_range'] = 'N/A';
        }
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->WriteHTML(view('admin.verifications.address_proofs.address_proofs_report_pdf', $data));
        $mpdf->Output('address_proofs_report_' . time() . '.pdf', 'D');
    }

    public function addressProofEdit($id)
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'address-proofs';

        $data['documentVerification'] = $documentVerification = DocumentVerification::find($id);
        return view('admin.verifications.address_proofs.edit', $data);
    }

    public function addressProofUpdate(Request $request)
    {
        $documentVerification         = DocumentVerification::find($request->id);
        $documentVerification->status = $request->status;
        $documentVerification->save();

        $user = User::find($request->user_id);
        if ($request->verification_type == 'address')
        {
            if ($request->status == 'approved')
            {
                $user->address_verified = true;
            }
            else
            {
                $user->address_verified = false;
            }
        }
        $user->save();

        if (checkDemoEnvironment() != true)
        {
            /**
             * Mail
             */
            $englishAddressVerificationEmailTemp = EmailTemplate::where(['temp_id' => 21, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();
            $addressVerificationEmailTemp        = EmailTemplate::where(['temp_id' => 21, 'language_id' => Session::get('default_language'), 'type' => 'email'])->select('subject', 'body')->first();

            if (!empty($addressVerificationEmailTemp->subject) && !empty($addressVerificationEmailTemp->body))
            {
                $addressVerificationEmailSub = str_replace('{Identity/Address}', 'Address', $addressVerificationEmailTemp->subject);

                $addressVerificationEmailBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $addressVerificationEmailTemp->body);
            }
            else
            {
                $addressVerificationEmailSub  = str_replace('{Identity/Address}', 'Address', $englishAddressVerificationEmailTemp->subject);
                $addressVerificationEmailBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $englishAddressVerificationEmailTemp->body);
            }
            $addressVerificationEmailBody = str_replace('{Identity/Address}', 'Address', $addressVerificationEmailBody);
            $addressVerificationEmailBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $addressVerificationEmailBody);
            $addressVerificationEmailBody = str_replace('{soft_name}', settings('name'), $addressVerificationEmailBody);

            if (checkAppMailEnvironment())
            {
                $this->email->sendEmail($user->email, $addressVerificationEmailSub, $addressVerificationEmailBody);
            }

            /**
             * SMS
             */
            $englishAddressVerificationSmsTemp = EmailTemplate::where(['temp_id' => 21, 'lang' => 'en', 'type' => 'sms'])->select('subject', 'body')->first();
            $addressVerificationSmsTemp        = EmailTemplate::where(['temp_id' => 21, 'language_id' => Session::get('default_language'), 'type' => 'sms'])->select('subject', 'body')->first();

            if (!empty($addressVerificationSmsTemp->subject) && !empty($addressVerificationSmsTemp->body))
            {
                $addressVerificationSmsSub  = str_replace('{Identity/Address}', 'Address', $addressVerificationSmsTemp->subject);
                $addressVerificationSmsBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $addressVerificationSmsTemp->body);
            }
            else
            {
                $addressVerificationSmsSub  = str_replace('{Identity/Address}', 'Address', $englishAddressVerificationSmsTemp->subject);
                $addressVerificationSmsBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $englishAddressVerificationSmsTemp->body);
            }
            $addressVerificationSmsBody = str_replace('{Identity/Address}', 'Address', $addressVerificationSmsBody);
            $addressVerificationSmsBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $addressVerificationSmsBody);

            if (!empty($user->carrierCode) && !empty($user->phone))
            {
                if (checkAppSmsEnvironment())
                {
                    sendSMS($user->carrierCode . $user->phone, $addressVerificationSmsBody);
                }
            }
        }
        $this->helper->one_time_message('success', __('The :x has been successfully verified.', ['x' => __('address')]));
        return redirect(Config::get('adminPrefix').'/address-proofs');
    }
}
