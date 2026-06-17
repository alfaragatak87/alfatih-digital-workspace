<?php
if (!defined('SITE_URL') || empty($_SESSION['uid'])) exit;

$owner = $_SESSION['username'];
// Proses aksi form jika ada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_todo'])) {
        $tugas = $mysqli->real_escape_string($_POST['tugas']);
        if (!empty($tugas)) {
            $mysqli->query("INSERT INTO todo_list (owner_username, tugas) VALUES ('$owner', '$tugas')");
            header("Location: index.php?page=todo&msg=success_add");
            exit;
        }
    } elseif (isset($_POST['selesai_todo'])) {
        $id = (int)$_POST['id'];
        $mysqli->query("UPDATE todo_list SET status='Selesai' WHERE id=$id AND owner_username='$owner'");
        header("Location: index.php?page=todo");
        exit;
    } elseif (isset($_POST['hapus_todo'])) {
        $id = (int)$_POST['id'];
        $mysqli->query("DELETE FROM todo_list WHERE id=$id AND owner_username='$owner'");
        header("Location: index.php?page=todo");
        exit;
    }
}

// Ambil data
$res = $mysqli->query("SELECT * FROM todo_list WHERE owner_username='$owner' ORDER BY status ASC, id DESC");
$todos = [];
if ($res) {
    while($r = $res->fetch_assoc()) $todos[] = $r;
}
?>

<div style="padding: 24px; max-width: 800px; margin: 0 auto; animation: fadeUp 0.5s ease;">
    <div style="text-align: center; margin-bottom: 32px;">
        <h1 style="font-family: var(--f-display); margin: 0 0 8px 0; font-size: 2.5rem;"><i class="fa-solid fa-list-check" style="color:var(--accent);"></i> To-Do List</h1>
        <p style="color: var(--text-muted); margin: 0;">Catat tugas harianmu dan tetap produktif.</p>
    </div>

    <!-- Form Tambah -->
    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; padding: 20px; box-shadow: var(--shadow-sm); margin-bottom: 24px;">
        <form method="POST" style="display: flex; gap: 12px;">
            <input type="text" name="tugas" placeholder="Tambahkan tugas baru..." required style="flex:1; padding: 14px 20px; border-radius: 12px; border: 1px solid var(--border); background: rgba(0,0,0,0.1); color: #fff; font-size: 1rem; outline: none;">
            <button type="submit" name="tambah_todo" style="background: linear-gradient(135deg, var(--accent), var(--accent-2)); border: none; padding: 0 24px; border-radius: 12px; color: #fff; font-weight: 600; cursor: pointer; transition: transform 0.2s;"><i class="fa-solid fa-plus"></i> Tambah</button>
        </form>
    </div>

    <!-- Daftar Tugas -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php if (empty($todos)): ?>
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fa-solid fa-check-double" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p>Semua tugas sudah selesai! Kamu luar biasa.</p>
            </div>
        <?php else: ?>
            <?php foreach($todos as $t): 
                $isDone = ($t['status'] === 'Selesai');
            ?>
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; transition: background 0.2s; <?= $isDone ? 'opacity: 0.6;' : '' ?>">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <button type="submit" name="selesai_todo" <?= $isDone ? 'disabled' : '' ?> style="background: <?= $isDone ? 'var(--success)' : 'transparent' ?>; border: 2px solid <?= $isDone ? 'var(--success)' : 'var(--border)' ?>; width: 24px; height: 24px; border-radius: 6px; cursor: <?= $isDone ? 'default' : 'pointer' ?>; color: #fff; display: flex; align-items: center; justify-content: center;">
                                <?= $isDone ? '<i class="fa-solid fa-check" style="font-size: 12px;"></i>' : '' ?>
                            </button>
                        </form>
                        <span style="font-size: 1.1rem; <?= $isDone ? 'text-decoration: line-through; color: var(--text-muted);' : '' ?>"><?= htmlspecialchars($t['tugas']) ?></span>
                    </div>
                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Hapus tugas ini?');">
                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                        <button type="submit" name="hapus_todo" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
