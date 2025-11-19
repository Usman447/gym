<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

class ZKTecoService
{
    /**
     * Extract numeric part from member_code (e.g., 'MEM123' -> 123)
     * 
     * @param string $memberCode
     * @return int|null
     */
    public static function extractMemberNumber($memberCode)
    {
        if (preg_match('/\d+/', $memberCode, $matches)) {
            return (int) $matches[0];
        }
        return null;
    }

    /**
     * Execute Python script and return JSON response
     * 
     * @param string $operation (add, check, remove)
     * @param int $memberId
     * @param string|null $name (required for 'add' operation)
     * @return array ['success' => bool, 'message' => string, ...]
     */
    protected static function executePythonScript($operation, $memberId, $name = null)
    {
        $pythonScript = config('zkteco.python_script_path');
        $pythonExecutable = config('zkteco.python_executable');

        $command = [
            $pythonExecutable,
            $pythonScript,
            $operation,
            (string) $memberId
        ];

        if ($operation === 'add' && $name !== null) {
            $command[] = $name;
        }

        try {
            $process = new Process($command);
            $process->setTimeout(60);
            $process->setWorkingDirectory(base_path());
            $process->run();

            $output = trim($process->getOutput());
            
            // Try to parse JSON output
            $result = json_decode($output, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($result)) {
                return $result;
            }

            // If JSON parsing failed, check if process was successful
            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                Log::error('ZKTeco Python script error', [
                    'operation' => $operation,
                    'member_id' => $memberId,
                    'error' => $errorOutput,
                    'output' => $output
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to execute Python script: ' . ($errorOutput ?: 'Unknown error')
                ];
            }

            // If we got here but no JSON, return error
            return [
                'success' => false,
                'message' => 'Invalid response from Python script: ' . $output
            ];

        } catch (ProcessFailedException $e) {
            Log::error('ZKTeco Process failed', [
                'operation' => $operation,
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Process execution failed: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('ZKTeco Exception', [
                'operation' => $operation,
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Add member to ZKTeco device
     * 
     * @param string $memberCode (e.g., 'MEM123')
     * @param string $name
     * @return array ['success' => bool, 'message' => string]
     */
    public static function addMemberToDevice($memberCode, $name)
    {
        $memberId = self::extractMemberNumber($memberCode);
        
        if ($memberId === null) {
            return [
                'success' => false,
                'message' => 'Could not extract member number from member_code: ' . $memberCode
            ];
        }

        return self::executePythonScript('add', $memberId, $name);
    }

    /**
     * Check if member exists in ZKTeco device
     * 
     * @param string $memberCode (e.g., 'MEM123')
     * @return array ['success' => bool, 'exists' => bool, 'message' => string]
     */
    public static function checkMemberInDevice($memberCode)
    {
        $memberId = self::extractMemberNumber($memberCode);
        
        if ($memberId === null) {
            return [
                'success' => false,
                'exists' => false,
                'message' => 'Could not extract member number from member_code: ' . $memberCode
            ];
        }

        return self::executePythonScript('check', $memberId);
    }

    /**
     * Check if member has fingerprint configured in ZKTeco device
     * 
     * @param string $memberCode (e.g., 'MEM123')
     * @return array ['success' => bool, 'has_fingerprint' => bool, 'message' => string]
     */
    public static function checkMemberFingerprint($memberCode)
    {
        $memberId = self::extractMemberNumber($memberCode);
        
        if ($memberId === null) {
            return [
                'success' => false,
                'has_fingerprint' => false,
                'message' => 'Could not extract member number from member_code: ' . $memberCode
            ];
        }

        return self::executePythonScript('fingerprint', $memberId);
    }

    /**
     * Remove member from ZKTeco device
     * 
     * @param string $memberCode (e.g., 'MEM123')
     * @return array ['success' => bool, 'message' => string]
     */
    public static function removeMemberFromDevice($memberCode)
    {
        $memberId = self::extractMemberNumber($memberCode);
        
        if ($memberId === null) {
            return [
                'success' => false,
                'message' => 'Could not extract member number from member_code: ' . $memberCode
            ];
        }

        return self::executePythonScript('remove', $memberId);
    }
}

