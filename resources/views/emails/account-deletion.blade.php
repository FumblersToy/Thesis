<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table width="600" cellpadding="0" cellspacing="0" style="margin: 0 auto; font-family: Arial, sans-serif;">
        <tr>
            <td bgcolor="#667eea" style="padding: 30px; text-align: center; color: white;">
                <h1>Account Deletion Notice</h1>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f9f9f9" style="padding: 30px;">
                <p>Dear @php echo $user->name; @endphp,</p>
                
                <table width="100%" cellpadding="20" style="background: #fff3cd; border: 2px solid #ffc107; margin: 20px 0;">
                    <tr>
                        <td>
                            <h2 style="margin-top: 0; color: #856404;">Your account has been scheduled for deletion</h2>
                            <p><strong>Reason:</strong> @php echo $reason; @endphp</p>
                        </td>
                    </tr>
                </table>
                
                <p style="font-size: 24px; font-weight: bold; color: #dc3545; text-align: center;">
                    @php echo $daysUntilDeletion; @endphp days until permanent deletion
                </p>
                
                <table width="100%" cellpadding="20" style="background: #d1ecf1; border: 2px solid #17a2b8; margin: 20px 0;">
                    <tr>
                        <td>
                            <h3 style="margin-top: 0; color: #0c5460;">What happens next?</h3>
                            <ul>
                                <li>Your account will be permanently deleted in @php echo $daysUntilDeletion; @endphp days</li>
                                <li>All your posts, comments, and data will be removed</li>
                                <li>This action cannot be undone after the deadline</li>
                            </ul>
                        </td>
                    </tr>
                </table>
                
                <h3>Submit an Appeal</h3>
                <p>If you believe this action was taken in error or would like to contest this decision, you can submit an appeal. Our admin team will review your case.</p>
                
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" style="padding: 20px 0;">
                            <a href="@php echo $appealUrl; @endphp" style="display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; font-weight: bold;">Submit Appeal</a>
                        </td>
                    </tr>
                </table>
                
                <p style="margin-top: 30px; font-size: 14px; color: #666;">
                    <strong>Important:</strong> You must submit your appeal before the deadline. After @php echo $daysUntilDeletion; @endphp days, your account will be permanently deleted and cannot be recovered.
                </p>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding: 20px; color: #666; font-size: 12px; border-top: 1px solid #ddd;">
                <p>This is an automated message from the Admin Team</p>
                <p>If you did not request this action, please contact support immediately</p>
            </td>
        </tr>
    </table>
</body>
</html>
