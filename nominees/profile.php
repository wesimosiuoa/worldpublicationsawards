<?php 
include 'header.php';


// Verify session security
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'nominee') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'] ?? '';
$message = '';
$message_type = '';

// Handle social media link operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Security validation failed. Please try again.';
        $message_type = 'danger';
    } elseif (isset($_POST['add_social_media'])) {
        // Add social media link
        $platform = htmlspecialchars($_POST['platform'] ?? '', ENT_QUOTES, 'UTF-8');
        $link = htmlspecialchars($_POST['social_link'] ?? '', ENT_QUOTES, 'UTF-8');
        
        // Validate inputs
        if (empty($platform) || empty($link)) {
            $message = 'Platform and link are required.';
            $message_type = 'danger';
        } elseif (!filter_var($link, FILTER_VALIDATE_URL)) {
            $message = 'Please enter a valid URL.';
            $message_type = 'danger';
        } else {
            try {
                // Get nominee ID
                $stmt = $pdo->prepare("SELECT id FROM nominees WHERE email = ? OR contact_person_email = ?");
                $stmt->execute([$user_email, $user_email]);
                $nominee = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($nominee) {
                    $nominee_id = $nominee['id'];
                    
                    // Check if platform already exists for this nominee
                    $stmt = $pdo->prepare("SELECT platform_id FROM nominees_social_media_links WHERE nominee_id = ? AND platform_name = ?");
                    $stmt->execute([$nominee_id, $platform]);
                    
                    if ($stmt->rowCount() > 0) {
                        // Update existing
                        $stmt = $pdo->prepare("UPDATE nominees_social_media_links SET link = ?, updated_at = NOW() WHERE nominee_id = ? AND platform_name = ?");
                        $stmt->execute([$link, $nominee_id, $platform]);
                        $message = 'Social media link updated successfully!';
                        $message_type = 'success';
                    } else {
                        // Insert new
                        $stmt = $pdo->prepare("INSERT INTO `nominees_social_media_links` (`platform_id`, `platform_name`, `link`, `nominee_id`) VALUES (NULL, ?, ?, ?)");
                        $stmt->execute([$platform, $link, $nominee_id]);
                        $message = 'Social media link added successfully!';
                        $message_type = 'success';
                    }
                } else {
                    $message = 'Nominee record not found.';
                    $message_type = 'danger';
                }
            } catch(Exception $e) {
                $message = 'Error saving social media link. Please try again. ' . htmlspecialchars($e->getMessage() . ' sql error: ' . implode(' | ', $pdo->errorInfo()));
                $message_type = 'danger';
            }
        }
    } elseif (isset($_POST['delete_social_media'])) {
        // Delete social media link
        $link_id = intval($_POST['link_id'] ?? 0);
        
        if ($link_id > 0) {
            try {
                // Verify ownership before deleting
                $stmt = $pdo->prepare("
                    SELECT sl.id FROM nominees_social_media_links sl
                    JOIN nominees n ON sl.nominee_id = n.id
                    WHERE sl.id = ? AND (n.email = ? OR n.contact_person_email = ?)
                ");
                $stmt->execute([$link_id, $user_email, $user_email]);
                
                if ($stmt->rowCount() > 0) {
                    $stmt = $pdo->prepare("DELETE FROM nominees_social_media_links WHERE id = ?");
                    $stmt->execute([$link_id]);
                    $message = 'Social media link deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Unauthorized action.';
                    $message_type = 'danger';
                }
            } catch(Exception $e) {
                $message = 'Error deleting social media link.';
                $message_type = 'danger';
            }
        }
    }
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id  = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../login.php');
    exit();
}

// Get nominee information
$stmt = $pdo->prepare("SELECT * FROM nominees WHERE email = ? OR contact_person_email = ?");
$stmt->execute([$user_email, $user_email]);
$nominee = $stmt->fetch(PDO::FETCH_ASSOC);

// Get nominee social media links
$social_links = [];
if ($nominee) {
    $stmt = $pdo->prepare("SELECT platform_id , platform_name, link FROM nominees_social_media_links WHERE nominee_id = ? ORDER BY nominee_id DESC");
    $stmt->execute([$nominee['id']]);
    $social_links = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if user table has name columns
$hasNameColumns = isset($user['first_name']) && isset($user['last_name']);
?>
<div class="container-fluid mt-4">
  <div class="row mb-3">
    <div class="col-12">
      <h3 class="fw-semibold">
        <i class="fas fa-share-alt me-2 text-primary"></i>
        Social Media Presence
      </h3>
      <p class="text-muted mb-0">
        Manage and update your official social media profiles visible to voters.
      </p>
    </div>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-<?= htmlspecialchars($message_type) ?> alert-dismissible fade show">
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- LEFT: NOMINEE INFO -->
    <div class="col-lg-4 mb-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="mb-3">
            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                 style="width:90px;height:90px;font-size:2.3rem;">
              <i class="fas fa-user text-primary"></i>
            </div>
          </div>

          <h5 class="mb-0">
            <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
          </h5>
          <small class="text-muted">@<?= htmlspecialchars($user['username'] ?? $user_id) ?></small>

          <div class="mt-2">
            <span class="badge bg-success">Nominee</span>
          </div>

          <hr>

          <div class="text-start small text-muted">
            <p class="mb-1">
              <i class="fas fa-envelope me-2"></i><?= htmlspecialchars($user['email']) ?>
            </p>
            <p class="mb-1">
              <i class="fas fa-calendar-alt me-2"></i>
              Joined <?= date('M Y', strtotime($user['created_at'])) ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: SOCIAL MEDIA MANAGEMENT -->
    <div class="col-lg-8">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
          <h5 class="mb-0">
            <i class="fas fa-plus-circle me-2 text-success"></i>
            Add / Update Social Media Link
          </h5>
        </div>

        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Platform</label>
                <select name="platform" class="form-select" required>
                  <option value="">Select</option>
                  <option>YouTube</option>
                  <option>Instagram</option>
                  <option>Facebook</option>
                  <option>Twitter</option>
                  <option>LinkedIn</option>
                  <option>TikTok</option>
                  <option>Website</option>
                </select>
              </div>

              <div class="col-md-8">
                <label class="form-label">Profile URL</label>
                <input type="url" name="social_link" class="form-control"
                       placeholder="https://..." required>
              </div>
            </div>

            <div class="mt-3 text-end">
              <button type="submit" name="add_social_media" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Save Link
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- EXISTING LINKS -->
      <div class="card shadow-sm">
        <div class="card-header bg-white">
          <h5 class="mb-0">
            <i class="fas fa-list me-2 text-primary"></i>
            Your Social Media Links
          </h5>
        </div>

        <div class="card-body p-0">
          <?php if ($social_links): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Platform</th>
                    <th>Link</th>
                    <th class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($social_links as $link): ?>
                  <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($link['platform_name']) ?></td>
                    <td>
                      <a href="<?= htmlspecialchars($link['link']) ?>" target="_blank">
                        <?= htmlspecialchars($link['link']) ?>
                      </a>
                    </td>
                    <td class="text-end">
                      <form method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="link_id" value="<?= (int)$link['platform_id'] ?>">
                        <button name="delete_social_media"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Delete this link?')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="p-4 text-center text-muted">
              <i class="fas fa-info-circle me-2"></i>No social media links added yet.
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>



