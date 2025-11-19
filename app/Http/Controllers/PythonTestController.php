<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PythonTestController extends Controller
{
    /**
     * Display the test page
     */
    public function index()
    {
        return view('python_test.index');
    }

    /**
     * Execute Python script with parameter
     */
    public function execute(Request $request)
    {
        $parameter = $request->input('parameter', 'default_value');
        
        // Validate parameter (basic example)
        if (empty($parameter)) {
            return response()->json([
                'success' => false,
                'error' => 'Parameter is required'
            ], 400);
        }

        // Method 1: Using Symfony Process (Recommended - More secure and robust)
        try {
            // Get the path to Python script
            $pythonScript = base_path('python_scripts/test_script.py');
            
            // Create process to run Python script
            $process = new Process([
                'python3',  // or 'python' depending on your system
                $pythonScript,
                $parameter
            ]);
            
            // Set timeout (in seconds)
            $process->setTimeout(60);
            
            // Set working directory
            $process->setWorkingDirectory(base_path());
            
            // Run the process
            $process->run();
            
            // Check if process was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            
            // Get output
            $output = $process->getOutput();
            
            return response()->json([
                'success' => true,
                'parameter' => $parameter,
                'output' => $output,
                'method' => 'Symfony Process'
            ]);
            
        } catch (ProcessFailedException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'output' => $process->getErrorOutput()
            ], 500);
        }
        
        // Alternative Method 2: Using exec() (Simple but less secure)
        /*
        $pythonScript = base_path('python_scripts/test_script.py');
        $command = escapeshellcmd("python3 {$pythonScript}") . ' ' . escapeshellarg($parameter);
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            return response()->json([
                'success' => true,
                'parameter' => $parameter,
                'output' => implode("\n", $output),
                'method' => 'exec()'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Python script execution failed',
                'return_code' => $returnCode
            ], 500);
        }
        */
    }
}

