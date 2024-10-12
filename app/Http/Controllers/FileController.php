<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Group;
use App\Models\Report;
use App\Traits\ReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class FileController extends Controller
{
    use ReturnResponse;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048', // Adjust the file validation as needed
            'group_id' => 'required|exists:groups,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $file = $request->file('file');
        $path = public_path() . 'Files';
        $filename = $file->getClientOriginalName();
        $userId = Auth::id();
        $file2 = File::create([
            'file_name' => $filename,
            'created_date' => now(),
            'state' => false,
            'path' => $path,
            'file' => $file,
            'group_id' => $request['group_id'],
            'user_id' => $userId
        ]);
        if ($file->move($path, $filename)) {
            return $this->returnData('file:', $file2, 'success');
        }
        return $this->returnError(404, 'error');
    }

    public function downloadFile($id)
    {
        $file = File::find($id);
        $filename = $file->file_name;
        $path = public_path() . 'Files/' . $filename;
        if (!file_exists($path)) {
            abort(404, 'file not found');
        }
        return response()->download($path, $filename, [
            'Content-Type' => ['application/pdf',
                'application/msword',
                'application/vnd.ms-excel'],
            'Content-Disposition' => 'inline;filename="' . $filename . '"'
        ]);
    }

    public function chechIn($id)
    {
        $file = File::find($id);
        if ($file->state == 1) {
            return $this->returnError(404, 'file is reservation');
        } else if ($file->state == 0) {
            $firstDate = now();
            $file->created_date = $firstDate;
            $file->state = 1;
        }
        $file->save();
        return $this->returnData('firstdate', $firstDate, 'success');
    }

//    public function upFile($filename)
//    {
//        $existingFile = public_path() . 'Files' . '/' . $filename;
//        if (file_exists($existingFile)) {
//            unlink($existingFile);
//        }
//        return $this->returnSuccessMessage('file uploaded successfully');
//    }
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'group_id' => 'required|exists:groups,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $file = $request->file('file');
        $fileName= $file->getClientOriginalName();
        $filePath = public_path().'Files'. $fileName;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $file->move(public_path(). 'Files', $fileName);

        return $this->returnSuccessMessage('File uploaded successfully');
    }

    /**
     * Release reservation of a file
     */
    public function checkout($id)
    {
        $file = File::find($id);
        $file->state = 0;
        $file->save();
        $endDate = now();
        return $this->returnData('endDate:', $endDate, 'success');
    }

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048|mimes:jpeg,png,pdf',
//            'files.*' => 'required|file|max:2048|mimes:jpeg,png,pdf',
            'group_id' => 'required'
        ]);
        $userId = Auth::id();
        if ($request->hasFile('file')) {
            foreach ($request->files as $file) {
                $path = public_path() . 'Files';
                $filename = $file->getClientOriginalName();
                $file->move($path, $filename);
                $file2 = new File();
                $file2->fill([
                    'file_name' => $filename,
                    'created_date' => now(),
                    'state' => false,
                    'path' => $path,
                    'file' => $file,
                    'group_id' => $request['group_id'],
                    'user_id' => $userId
                ]);
                $file2->save();
                return $this->returnSuccessMessage('files uploaded successfully');
            }
        }
        return $this->returnError(222, 'invalid');
    }

}
