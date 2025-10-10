<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Define permission mappings based on your current frontend logic
        $userPermissions = $this->getUserPermissions($user);

        // Check if user has any of the required permissions
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403, 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }

    /**
     * Get user permissions based on role and emp_code
     */
    private function getUserPermissions($user): array
    {
        $permissions = [];
        $role = $user->role;
        $empCode = $user->emp_code;

        // Admin has all permissions for both GGSS and GCO
        if ($role === 'Admin') {
            return [
                'dashboard_access',
                'survey_monitoring_access',
                'sales_revenue_access',
                'quotation_access',
                'expenses_access',
                'office_equipment_access',
                'survey_equipment_access',
                'company_assets_access',
                'govt_external_access',
                'titles_access',
                'gco_access' //new permission for GCO routes
            ];
        }

        //emp code specific permissions
        switch ($empCode) {
            case '0000-0002':
                $permissions[] = 'survey_monitoring_access';
                $permissions[] = 'quotation_access';
                $permissions[] = 'office_equipment_access';
                $permissions[] = 'survey_equipment_access';
                $permissions[] = 'company_assets_access';
                $permissions[] = 'titles_access';
                $permissions[] = 'gco_access'; //GCO and construction project monitoring access
                break;
                
            case '0000-0004':
                $permissions[] = 'survey_monitoring_access';
                $permissions[] = 'company_assets_access';
                $permissions[] = 'govt_external_access';
                $permissions[] = 'titles_access';
                $permissions[] = 'gco_access'; //GCO and construction project monitoring access
                break;

            case '0000-0016':
                //handled in the 'Encoder' role logic below, as it's a special case
                break;
        }

        //role-based permissions, including the new Encoder rules
        if($role === 'Encoder'){
            if($empCode === '0000-0016'){
                $permissions[] = 'survey_monitoring_access';
                $permissions[] = 'survey_equipment_access';
                $permissions[] = 'govt_external_access';
                $permissions[] = 'titles_access';
            }
            else{ // Encoder but not 0000-0016
                $permissions[] = 'survey_monitoring_access';
                $permissions[] = 'survey_equipment_access';
                $permissions[] = 'titles_access';
            }
        }
        elseif($role === 'Checker'){
            //checkers have specific access to survey equipment
            if($empCode === '0000-0011'){
                $permissions[] = 'survey_equipment_access';
            }
        }
        
        return array_unique($permissions);
    }
}
