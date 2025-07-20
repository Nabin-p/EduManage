<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAcademicInfos;

class KhaltiPaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        // You can retrieve the student's info based on ID
        $studentInfo = StudentAcademicInfos::where('student_id', $request->student_id)->first();
        
        if ($studentInfo->is_paid) {
            return \response()->json(['message' => 'Fee already paid.'], 400);
        }

        // Redirect to Khalti's payment page, generate Khalti Payment Request
        // You will need to pass the amount and Khalti's public key here
        // Example response data based on Khalti's API
    }

    public function verifyPayment(Request $request)
    {
        // Verify Khalti transaction via Khalti API
        // Use the token and amount to validate the transaction
        
        // If successful, update the student's payment status
        $studentInfo = StudentAcademicInfo::where('student_id', $request->student_id)->first();
        if ($studentInfo && $request->status == 'success') {
            $studentInfo->update([
                'is_paid' => true
            ]);
        }

        return response()->json(['message' => 'Payment successful']);
    }
}
