<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0;">⚠️ Account Deletion Notice</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border: 1px solid #ddd;">
        <p>Dear {{ $user->name }},</p>
        
        <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h2 style="margin-top: 0; color: #856404;">Your account has been scheduled for deletion</h2>
            <p><strong>Reason:</strong> {{ $reason }}</p>
        </div>
        
        <div style="font-size: 24px; font-weight: bold; color: #dc3545; text-align: center; margin: 20px 0;">
            🕐 {{ $daysUntilDeletion }} days until permanent deletion
        </div>
        
        <div style="background: #d1ecf1; border: 2px solid #17a2b8; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #0c5460;">What happens next?</h3>
            <ul>
                <li>Your account will be permanently deleted in {{ $daysUntilDeletion }} days</li>
                <li>All your posts, comments, and data will be removed</li>
                <li>This action cannot be undone after the deadline</li>
            </ul>
        </div>
        
        <h3>🙏 Submit an Appeal</h3>
        <p>If you believe this action was taken in error or would like to contest this decision, you can submit an appeal. Our admin team will review your case.</p>
        
        <div style="text-align: center;">
            <a href="{{ $appealUrl }}" style="display: inline-block; padding: 15px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0;">Submit Appeal</a>
        </div>
        
        <p style="margin-top: 30px; font-size: 14px; color: #666;">
            <strong>Important:</strong> You must submit your appeal before the deadline. After {{ $daysUntilDeletion }} days, your account will be permanently deleted and cannot be recovered.
        </p>
    </div>
    
    <div style="text-align: center; padding: 20px; color: #666; font-size: 12px; border-top: 1px solid #ddd; margin-top: 20px;">
        <p>This is an automated message from the Admin Team</p>
        <p>If you did not request this action, please contact support immediately</p>
    </div>
</body>
</html>
