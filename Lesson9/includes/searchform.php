<!-- ======================================================= -->
<!-- FORM 2: GROUP BY AGGREGATIONS                           -->
<!-- ======================================================= -->
<fieldset style="margin-bottom: 25px; padding: 15px; border: 1px solid #ccc; border-radius: 6px;">
    <legend><strong>Group & Summarize Data</strong></legend>
    <form method="GET" action="" style="display: flex; gap: 15px; align-items: flex-end;">
        <div>
            <label for="group_by">Group By Column:</label><br>
            <select name="group_by" id="group_by" required>
                <option value="">-- Select Column --</option>
                <option value="gender" <?= $groupField === 'gender' ? 'selected' : '' ?>>Gender</option>
                <option value="membership" <?= $groupField === 'membership' ? 'selected' : '' ?>>Membership Type</option>
                <option value="newsletter" <?= $groupField === 'newsletter' ? 'selected' : '' ?>>Newsletter Status</option>
            </select>
        </div>
        <button type="submit">Analyze Groups</button>
    </form>

    <?php if (!empty($groupedResults)): ?>
        <div style="margin-top: 15px;">
            <h4>Aggregation Results for: <em><?= htmlspecialchars($groupField) ?></em></h4>
            <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; text-align: left;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th>Value</th>
                        <th>Total Count</th>
                        <th>Average Age</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupedResults as $group): ?>
                        <tr>
                            <td>
                                <?php 
                                if ($groupField === 'newsletter') {
                                    echo $group[$groupField] ? 'Subscribed (Yes)' : 'Unsubscribed (No)';
                                } else {
                                    echo htmlspecialchars($group[$groupField] ?? 'N/A');
                                }
                                ?>
                            </td>
                            <td><?= $group['total_count'] ?></td>
                            <td><?= round($group['avg_age'], 1) ?> yrs</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</fieldset>

<!-- ======================================================= -->
<!-- FORM 3: SELECT DISTINCT FIELD VALUES                    -->
<!-- ======================================================= -->
<fieldset style="margin-bottom: 25px; padding: 15px; border: 1px solid #ccc; border-radius: 6px;">
    <legend><strong>Select Distinct Values</strong></legend>
    <form method="GET" action="" style="display: flex; gap: 15px; align-items: flex-end;">
        <div>
            <label for="distinct_field">Unique Field:</label><br>
            <select name="distinct_field" id="distinct_field" required>
                <option value="">-- Select Field --</option>
                <option value="first_name" <?= $distinctField === 'first_name' ? 'selected' : '' ?>>First Name</option>
                <option value="last_name" <?= $distinctField === 'last_name' ? 'selected' : '' ?>>Last Name</option>
                <option value="nickname" <?= $distinctField === 'nickname' ? 'selected' : '' ?>>Nickname</option>
            </select>
        </div>
        <button type="submit">Get Distinct Values</button>
    </form>

    <?php if (!empty($distinctResults)): ?>
        <div style="margin-top: 15px;">
            <h4>Unique <em><?= htmlspecialchars($distinctField) ?></em> values (<?= count($distinctResults) ?> total):</h4>
            <p style="font-size: 0.85em; color: #555; margin-bottom: 8px;"><em>Click any value below to filter records by exact match:</em></p>
            <ul style="column-count: 3; column-gap: 20px; margin: 0; padding-left: 20px;">
                <?php foreach ($distinctResults as $value): ?>
                    <li>
                        <?php if (in_array($distinctField, ['first_name', 'last_name'], true)): ?>
                            <a href="?distinct_field=<?= urlencode($distinctField) ?>&filter_field=<?= urlencode($distinctField) ?>&filter_val=<?= urlencode($value) ?>">
                                <?= htmlspecialchars($value) ?>
                            </a>
                        <?php else: ?>
                            <?= htmlspecialchars($value) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($distinctField !== '' && empty($errorMessage)): ?>
        <p style="margin-top: 10px;">No distinct non-empty values found for <?= htmlspecialchars($distinctField) ?>.</p>
    <?php endif; ?>
</fieldset>

<!-- ======================================================= -->
<!-- FORM 1: ADVANCED SEARCH                                 -->
<!-- ======================================================= -->
<fieldset style="margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 6px;">
    <legend><strong>Filter Records</strong></legend>
    <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div>
            <label for="first_name">First Name:</label><br>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($searchFirstName) ?>">
        </div>

        <div>
            <label for="last_name">Last Name:</label><br>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($searchLastName) ?>">
        </div>

        <div>
            <label for="operator">Logic Condition:</label><br>
            <select name="operator" id="operator">
                <option value="AND" <?= $searchOperator === 'AND' ? 'selected' : '' ?>>AND (Both must match)</option>
                <option value="OR" <?= $searchOperator === 'OR' ? 'selected' : '' ?>>OR (Either matches)</option>
            </select>
        </div>

        <div>
            <label for="match_type">Match Type:</label><br>
            <select name="match_type" id="match_type">
                <option value="contains" <?= $searchMatchType === 'contains' ? 'selected' : '' ?>>Contains (%val%)</option>
                <option value="starts_with" <?= $searchMatchType === 'starts_with' ? 'selected' : '' ?>>Starts With (val%)</option>
                <option value="exact" <?= $searchMatchType === 'exact' ? 'selected' : '' ?>>Exact Match (= val)</option>
            </select>
        </div>

        <div>
            <button type="submit">Apply Filter</button>
            <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" style="margin-left: 5px; font-size: 0.9em;">Reset</a>
        </div>
    </form>
</fieldset>