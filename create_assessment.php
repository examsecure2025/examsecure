<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assessment | Exam Secure</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/create_assessment-style.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .back-button {
            background: #17305c;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .back-button:hover { background: #25477a; }
    </style>
</head>
<body>
<div class="create-assessment-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <button class="back-button" onclick="window.location.href='assessments.php'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Assessments
        </button>
        <div class="header">
            <span class="title">Create Assessment</span>
            <div style="display: flex; align-items: center; gap: 16px; position:relative;">
                <span class="profile-icon" id="profileIcon" style="cursor:pointer; position:relative;">
                    <i class="bi bi-person-circle" style="font-size:48px; color:#fff;"></i>
                </span>
                <div id="profileMenu" style="display:none; position:absolute; top:64px; right:0; background:#11224a; border:none; border-radius:0.75rem; padding:0.5rem; box-shadow:0 10px 30px rgba(0,0,0,0.35); z-index:10000; min-width: 220px;">
                    <span style="position:absolute; top:-6px; right:24px; width:12px; height:12px; background:#11224a; transform: rotate(45deg);"></span>
                    <div style="padding:0.5rem 0.75rem; color:#b3c6e0; font-size:0.9rem; border-bottom:1px solid #25477a; margin-bottom:0.25rem;">Profile</div>
                    <button type="button" onclick="window.location.href='account_settings.php'" style="background:transparent; color:#fff; border:none; border-radius:0.5rem; padding:0.6rem 0.75rem; font-weight:600; cursor:pointer; width:100%; text-align:left; display:flex; align-items:center; gap:0.5rem;" onmouseover="this.style.background='#17305c'" onmouseout="this.style.background='transparent'">
                        <i class="bi bi-gear" style="font-size:1rem;"></i>
                        <span>Account Settings</span>
                    </button>
                </div>
            </div>
        </div>
        <form class="assessment-details-form">
            <section class="details-section">
                <div class="section-title">Details</div>
                <div class="details-grid">
                    <div class="form-group">
                        <label for="title">Title of the test</label>
                        <input type="text" id="title" placeholder="Enter title">
                    </div>
                    <div class="form-group">
                        <label for="year_course">Year & Course</label>
                        <input type="text" id="year_course" placeholder="Enter Y & C">
                        <span class="input-hint">Format : III BSIT</span>
                    </div>
                    <div class="form-group">
                        <label for="section">Add Section</label>
                        <div class="section-input-row">
                            <input type="text" id="section" placeholder="Add section">
                            
                        </div>
                        <span class="input-hint">Format : A,B,D</span>
                    </div>
                    <div class="form-group">
                        <label for="course_code">Course Code</label>
                        <input type="text" id="course_code" placeholder="Enter course code">
                    </div>
                    <div class="form-group">
                        <label for="timer">Timer</label>
                        <select id="timer">
                        <option selected disabled>Select timer</option>
                            <option selected>No time limit</option>
                            <option>60 minutes</option>
                            <option>30 minutes</option>
                            <option>90 minutes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status">
                            <option>Active</option>
                            <option>Close</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="school_year">School Year</label>
                        <input type="text" id="school_year" placeholder="Enter S.Y">
                        <span class="input-hint">Format : 2024-2025</span>
                    </div>
                    <div class="form-group schedule-group">
                        <label>Schedule Assessment</label>
                        <div class="datetime-row">
                            <input type="date" id="schedule_date">
                            <select id="schedule_time">
                                <option selected disabled>Choose time</option>
                                <option>08:00 AM</option>
                                <option>09:00 AM</option>
                                <option>10:00 AM</option>
                                <option>11:00 AM</option>
                                <option>12:00 PM</option>
                                <option>01:00 PM</option>
                                <option>02:00 PM</option>
                                <option>03:00 PM</option>
                                <option>04:00 PM</option>
                                <option>05:00 PM</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group schedule-group">
                        <label>Automatically Close Assessment</label>
                        <div class="datetime-row">
                            <input type="date" id="close_date">
                            <select id="close_time">
                            <option selected disabled>Choose time</option>
                                <option>08:00 AM</option>
                                <option>09:00 AM</option>
                                <option>10:00 AM</option>
                                <option>11:00 AM</option>
                                <option>12:00 PM</option>
                                <option>01:00 PM</option>
                                <option>02:00 PM</option>
                                <option>03:00 PM</option>
                                <option>04:00 PM</option>
                                <option>05:00 PM</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>
            <section class="questions-section">
                <div class="section-title">Create Questions</div>
                
                <div class="question-type-buttons">
                    <div class="question-type-row mcq-row">
                        <button type="button" class="question-btn mcq" id="btn-mcq">
                            Create Multiple Choice type questions <span class="mcq-toggle-icon">+</span>
                        </button>
                        <div class="mcq-divider-line"></div>
                        <button type="button" class="slideup-btn mcq-caret" id="slideup-mcq" style="display:none;">&#9650;</button>
                    </div>
                    <div class="add-question-form" id="form-mcq" style="display:none;">
                        <div class="mcq-form-header">
                            <div class="mcq-header-left">
                                <div class="form-group-inline">
                                    <label>Shuffle Questions</label>
                                    <select class="mcq-shuffle">
                                        <option>On</option>
                                        <option>Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mcq-header-right">
                                <span class="mcq-questions-made-label">Questions made : <span class="mcq-questions-made">0</span></span>
                            </div>
                        </div>
                        <div class="mcq-form-body">
                            <div class="mcq-question-row">
                                <div class="mcq-question-number-label">Question #<span class="mcq-question-number">1</span></div>
                                <div class="form-group-inline">
                                    <label>Point/s</label>
                                    <select class="mcq-points">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" class="mcq-question-input" placeholder="Enter Question">
                            <hr class="mcq-divider">
                            <div class="mcq-choices-header">
                                <label>Choices</label>
                                <select class="mcq-choice-count">
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                            <div class="mcq-choices-list">
                                <div class="mcq-choice-row"><span class="mcq-choice-label">A</span><input type="text" class="mcq-choice-input" placeholder="Enter Choice A"></div>
                                <div class="mcq-choice-row"><span class="mcq-choice-label">B</span><input type="text" class="mcq-choice-input" placeholder="Enter Choice B"></div>
                                <div class="mcq-choice-row"><span class="mcq-choice-label">C</span><input type="text" class="mcq-choice-input" placeholder="Enter Choice C"></div>
                            </div>
                            <div class="mcq-correct-answer-block">
                                <label>Correct Answer</label>
                                <select class="mcq-correct-answer">
                                    <option value="" selected disabled>Select correct answer</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                        </div>
                        <div class="mcq-form-footer">
                            <button type="button" class="mcq-delete-btn">Delete Question</button>
                            <div class="mcq-footer-right">
                                <button type="button" class="mcq-prev-btn">Previous</button>
                                <div class="mcq-tooltip-wrap">
                                    <button type="button" class="mcq-next-btn">Next</button>
                                    <div class="mcq-tooltip">Clicking Next will automatically<br> add a new question.</div>
                                    <button type="button" class="mcq-tooltip-btn">&#33;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="question-type-row id-row">
                        <button type="button" class="question-btn id" id="btn-id">
                            Create Identification type questions <span class="mcq-toggle-icon">+</span>
                        </button>
                        <div class="id-divider-line"></div>
                        <button type="button" class="slideup-btn id-caret" id="slideup-id" style="display:none;">&#9650;</button>
                    </div>
                    <div class="add-question-form" id="form-id" style="display:none;">
                        <div class="idq-form-header">
                            <div class="idq-header-left">
                                <div class="idq-group-inline">
                                    <label>Shuffle Questions</label>
                                    <select class="idq-shuffle">
                                        <option>On</option>
                                        <option>Off</option>
                                    </select>
                                </div>
                                <div class="idq-group-inline">
                                    <label>Auto check student answer using AI</label>
                                    <select class="idq-ai-check">
                                        <option>On</option>
                                        <option>Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="idq-header-right">
                                <span class="idq-questions-made-label">Questions made : <span class="idq-questions-made">0</span></span>
                            </div>
                        </div>
                        <div class="idq-form-body">
                            <div class="idq-question-row">
                                <div class="idq-question-number-label">Question #<span class="idq-question-number">1</span></div>
                                <div class="idq-group-inline">
                                    <label>Point/s</label>
                                    <select class="idq-points">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" class="idq-question-input" placeholder="Enter Question">
                            <hr class="idq-divider">
                            <div class="idq-correct-answer-block">
                                <label>Correct Answer</label>
                                <input type="text" class="idq-correct-answer" placeholder="Enter Correct Answer">
                            </div>
                        </div>
                        <div class="idq-form-footer">
                            <button type="button" class="idq-delete-btn">Delete Question</button>
                            <div class="idq-footer-right">
                                <button type="button" class="idq-prev-btn">Previous</button>
                                <div class="idq-tooltip-wrap">
                                    <button type="button" class="idq-next-btn">Next</button>
                                    <div class="idq-tooltip">Clicking Next will automatically<br> add a new question.</div>
                                    <button type="button" class="idq-tooltip-btn">&#33;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="question-type-row tfq-row">
                        <button type="button" class="question-btn tf" id="btn-tf">
                            Create True or False type questions <span class="tfq-toggle-icon">+</span>
                        </button>
                        <div class="tfq-divider-line"></div>
                        <button type="button" class="slideup-btn tfq-caret" id="slideup-tf" style="display:none;">&#9650;</button>
                    </div>
                    <div class="add-question-form" id="form-tf" style="display:none;">
                        <div class="tfq-form-header">
                            <div class="tfq-header-left">
                                <div class="tfq-group-inline">
                                    <label>Shuffle Questions</label>
                                    <select class="tfq-shuffle">
                                        <option>On</option>
                                        <option>Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="tfq-header-right">
                                <span class="tfq-questions-made-label">Questions made : <span class="tfq-questions-made">0</span></span>
                            </div>
                        </div>
                        <div class="tfq-form-body">
                            <div class="tfq-question-row">
                                <div class="tfq-question-number-label">Question #<span class="tfq-question-number">1</span></div>
                                <div class="tfq-group-inline">
                                    <label>Point/s</label>
                                    <select class="tfq-points">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" class="tfq-question-input" placeholder="Enter Question">
                            <hr class="tfq-divider">
                            <div class="tfq-correct-answer-block">
                                <label>Correct Answer</label>
                                <select class="tfq-correct-answer">
                                <option selected disabled>Choose</option>
                                    <option>True</option>
                                    <option>False</option>
                                </select>
                            </div>
                        </div>
                        <div class="tfq-form-footer">
                            <button type="button" class="tfq-delete-btn">Delete Question</button>
                            <div class="tfq-footer-right">
                                <button type="button" class="tfq-prev-btn">Previous</button>
                                <div class="tfq-tooltip-wrap">
                                    <button type="button" class="tfq-next-btn">Next</button>
                                    <div class="tfq-tooltip">Clicking Next will automatically<br> add a new question.</div>
                                    <button type="button" class="tfq-tooltip-btn">&#33;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
        <button class="floating-btn" title="Scroll up">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#002147" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
        </button>
        <button class="create-assessment-btn" id="reviewAssessmentBtn">Review Assessment</button>
        <!-- Review Modal -->
        <div id="reviewModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(10,26,54,0.98); z-index:9999; overflow:auto;">
            <div class="modal-content" style="max-width:900px; margin:3rem auto; background:#0a1a36; border-radius:1.5rem; padding:2.5rem 2.5rem 2rem 2.5rem; box-shadow:0 0.25rem 1.5rem 0 rgba(0,0,0,0.18); position:relative;">
                <button id="closeReviewModal" style="position:absolute; top:1.5rem; right:2rem; background:none; border:none; color:#fff; font-size:2rem; cursor:pointer;">&times;</button>
                <div style="color:#fff;">
                    <div style="font-size:1.1rem; color:#b3c6e0; margin-bottom:0.5rem;">Create Assessment &gt; <span style="color:#fff;">Review</span></div>
                    <div class="section-title" style="margin-top:0;">Details</div>
                    <div class="details-grid" id="review-details-grid"></div>
                    <div class="section-title" style="margin-top:2.5rem;">Multiple Choice</div>
                    <div style="margin-bottom:0.5rem; color:#b3c6e0; font-size:1rem; display:flex; justify-content:space-between; align-items:center;">
                        <span id="review-mcq-shuffle"></span>
                        <span id="review-mcq-count"></span>
                    </div>
                    <div id="review-mcq-list"></div>
                    <div class="section-title" style="margin-top:2.5rem;">Identification</div>
                    <div style="margin-bottom:0.5rem; color:#b3c6e0; font-size:1rem; display:flex; justify-content:space-between; align-items:center;">
                        <span id="review-id-shuffle"></span>
                        <span id="review-id-ai"></span>
                        <span id="review-id-count"></span>
                    </div>
                    <div id="review-id-list"></div>
                    <div class="section-title" style="margin-top:2.5rem; display:none;" id="review-tf-title">True or False</div>
                    <div style="margin-bottom:0.5rem; color:#b3c6e0; font-size:1rem; display:flex; justify-content:space-between; align-items:center; display:none;" id="review-tf-meta">
                        <span id="review-tf-shuffle"></span>
                        <span id="review-tf-count"></span>
                    </div>
                    <div id="review-tf-list" style="display:none;"></div>
                    <button class="create-assessment-btn" id="publishAssessmentBtn" style="background:#ffe600; color:#002147; margin-top:2.5rem;">Publish Assessment</button>
                </div>
            </div>
        </div>
        <!-- Success Modal -->
        <div id="publishSuccessModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(10,26,54,0.98); z-index:9999; overflow:auto;">
            <div class="modal-content" style="max-width:700px; margin:5rem auto; background:#0a1a36; border-radius:1.5rem; padding:3rem 2.5rem 2.5rem 2.5rem; box-shadow:0 0.25rem 1.5rem 0 rgba(0,0,0,0.18); position:relative; text-align:center;">
                <svg width="64" height="64" viewBox="0 0 64 64" style="margin-bottom:1.5rem;">
                    <circle cx="32" cy="32" r="30" fill="#17305c"/>
                    <polyline points="18 34 29 45 46 22" fill="none" stroke="#2ecc40" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div style="font-size:2rem; font-weight:700; color:#fff; margin-bottom:0.5rem;">Assessment published successfully.</div>
                <div style="color:#b3c6e0; font-size:1.1rem; margin-bottom:2rem;">Students can now access it by entering this code in the application:</div>
                <div id="successAccessCode" style="font-size:2.5rem; font-weight:700; background:#19d3ff; color:#002147; border-radius:0.5rem; padding:0.7rem 2.5rem; display:inline-block; margin-bottom:2.5rem;"></div>
                <br>
                <button id="goToDashboardBtn" style="background:#ffe600; color:#002147; font-size:1.2rem; font-weight:700; border:none; border-radius:0.5rem; padding:0.8rem 2.5rem; cursor:pointer;">Go to Dashboard</button>
            </div>
        </div>
    </main>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/sidebar-active.js"></script>
<script src="js/create-assessment.js"></script>

<script>
$(function() {
    let mcqOpen = false;
    let idOpen = false;
    let tfqOpen = false;
    function resetMcqButton() {
        $('#btn-mcq').css({'background':'#17305c','color':'#fff'});
        $('#btn-mcq .mcq-toggle-icon').text('+');
        $('.mcq-divider-line').removeClass('active');
        $('#slideup-mcq').hide();
        mcqOpen = false;
    }
    function resetIdButton() {
        $('#btn-id').css({'background':'#17305c','color':'#fff'});
        $('#btn-id .mcq-toggle-icon').text('+');
        $('.id-divider-line').removeClass('active');
        $('#slideup-id').hide();
        idOpen = false;
    }
    function resetTfqButton() {
        $('#btn-tf').css({'background':'#17305c','color':'#fff'});
        $('#btn-tf .tfq-toggle-icon').text('+');
        $('.tfq-divider-line').removeClass('active');
        $('#slideup-tf').hide();
        tfqOpen = false;
    }
    $('#btn-mcq').click(function() {
        if (!mcqOpen) {
            resetMcqButton();
            $('#form-mcq').slideDown();
            $('#btn-mcq').css({'background':'#ffe600','color':'#111'});
            $('#btn-mcq .mcq-toggle-icon').text('−');
            $('.mcq-divider-line').addClass('active');
            $('#slideup-mcq').show();
            mcqOpen = true;
        } else {
            $('#form-mcq').slideUp();
            resetMcqButton();
        }
    });
    $('#slideup-mcq').click(function() {
        $('#form-mcq').slideUp();
        resetMcqButton();
    });
    $('#btn-id').click(function() {
        if (!idOpen) {
            resetIdButton();
            $('#form-id').slideDown();
            $('#btn-id').css({'background':'#ffe600','color':'#111'});
            if (!$('#btn-id .mcq-toggle-icon').length) {
                $('#btn-id').append(' <span class="mcq-toggle-icon">−</span>');
            } else {
                $('#btn-id .mcq-toggle-icon').text('−');
            }
            if (!$('.id-divider-line').length) {
                $('<div class="id-divider-line"></div>').insertAfter($('#btn-id'));
            }
            $('.id-divider-line').addClass('active');
            $('#slideup-id').show();
            idOpen = true;
        } else {
            $('#form-id').slideUp();
            resetIdButton();
        }
    });
    $('#slideup-id').click(function() {
        $('#form-id').slideUp();
        resetIdButton();
    });
    $('#btn-tf').click(function() {
        if (!tfqOpen) {
            resetTfqButton();
            $('#form-tf').slideDown();
            $('#btn-tf').css({'background':'#ffe600','color':'#111'});
            $('#btn-tf .tfq-toggle-icon').text('−');
            $('.tfq-divider-line').addClass('active');
            $('#slideup-tf').show();
            tfqOpen = true;
        } else {
            $('#form-tf').slideUp();
            resetTfqButton();
        }
    });
    $('#slideup-tf').click(function() {
        $('#form-tf').slideUp();
        resetTfqButton();
    });
    // Tooltip for Next button (MCQ)
    $('.mcq-tooltip-btn').on('mouseenter focus', function() {
        $(this).siblings('.mcq-tooltip').show();
    }).on('mouseleave blur', function() {
        $(this).siblings('.mcq-tooltip').hide();
    });
    // Tooltip for Next button (Identification)
    $('.idq-tooltip-btn').on('mouseenter focus', function() {
        $(this).siblings('.idq-tooltip').show();
    }).on('mouseleave blur', function() {
        $(this).siblings('.idq-tooltip').hide();
    });
    // Tooltip for Next button (True/False)
    $('.tfq-tooltip-btn').on('mouseenter focus', function() {
        $(this).siblings('.tfq-tooltip').show();
    }).on('mouseleave blur', function() {
        $(this).siblings('.tfq-tooltip').hide();
    });
    // Choices count change
    $('.mcq-choice-count').on('change', function() {
        var count = parseInt($(this).val());
        var labels = ['A','B','C','D','E'];
        var $list = $(this).closest('.mcq-form-body').find('.mcq-choices-list');
        $list.empty();
        for (var i=0; i<count; i++) {
            $list.append('<div class="mcq-choice-row"><span class="mcq-choice-label">'+labels[i]+'</span><input type="text" class="mcq-choice-input" placeholder="Enter Choice '+labels[i]+'"></div>');
        }
    });
});
// Profile tooltip toggle
document.addEventListener('click', function(e) {
    const icon = document.getElementById('profileIcon');
    const menu = document.getElementById('profileMenu');
    if (!icon || !menu) return;
    if (icon.contains(e.target)) {
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
    } else if (!menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});
</script>
</body>
</html>
