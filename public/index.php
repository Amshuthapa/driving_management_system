<?php
include_once __DIR__ . '/../includes/header.php';
?>

<style>
:root{
  --primary-blue:#2563eb;
  --primary-blue-dark:#1e40af;
  --glass-border: rgba(255,255,255,0.40);
  --glass-hi: rgba(255,255,255,0.75);
}

/* background */
body{
  background:
    radial-gradient(1200px 600px at 10% 10%, #c7d2fe, transparent 60%),
    radial-gradient(1000px 500px at 90% 20%, #e0e7ff, transparent 55%),
    linear-gradient(135deg, #eef2ff, #f8fafc);
}

/* glass card */
.glass-card{
  background: linear-gradient(135deg, rgba(255,255,255,0.80), rgba(255,255,255,0.50));
  backdrop-filter: blur(22px) saturate(160%);
  -webkit-backdrop-filter: blur(22px) saturate(160%);
  border: 1px solid var(--glass-border);
  border-radius: 20px;
  box-shadow: 0 30px 60px -20px rgba(0,0,0,0.18), inset 0 1px 0 var(--glass-hi);
  position: relative;
}

/* subtle glow */
.glass-card::before{
  content:"";
  position:absolute;
  inset:-1px;
  border-radius: inherit;
  background: linear-gradient(120deg, transparent, rgba(37,99,235,0.28), transparent);
  opacity: .75;
  pointer-events:none;
}

.section-title{ color:#0f172a; }
.section-subtitle{ color:#475569; }

/* buttons */
.btn-primary{
  background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
  border: none;
  border-radius: 14px;
  font-weight: 600;
}
.btn-primary:hover{
  transform: translateY(-1px);
  box-shadow: 0 14px 26px rgba(37,99,235,0.30);
}
.btn-outline-primary{
  border-radius: 14px;
  font-weight: 600;
}

/* hover cards */
.feature-card{
  transition: transform .2s ease, box-shadow .2s ease;
}
.feature-card:hover{
  transform: translateY(-4px);
  box-shadow: 0 20px 40px -22px rgba(0,0,0,0.25);
}

/* input focus */
.form-control:focus{
  border-color: var(--primary-blue);
  box-shadow: 0 0 0 0.15rem rgba(37, 99, 235, 0.25);
}

/* hero */
.hero-wrap{
  position: relative;
  overflow: hidden;
  padding-top: 2rem;
  padding-bottom: 2rem;
}
.hero-wrap::before{
  content:"";
  position:absolute;
  inset:0;
  background:
    radial-gradient(650px 320px at 15% 20%, rgba(37,99,235,0.25), transparent 60%),
    radial-gradient(750px 380px at 85% 25%, rgba(30,64,175,0.18), transparent 60%);
  pointer-events:none;
}

.mini-stat{
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.5);
  background: rgba(255,255,255,0.55);
  backdrop-filter: blur(14px);
}

.badge-soft{
  background: rgba(37,99,235,0.12);
  color: var(--primary-blue);
  border: 1px solid rgba(37,99,235,0.18);
}

hr.soft{
  opacity: 0.12;
}
</style>

<main>

<!-- HERO -->
<section class="hero-wrap">
  <div class="container position-relative">
    <div class="row align-items-center g-4">

      <div class="col-lg-7">
        <span class="badge badge-soft rounded-pill px-3 py-2 mb-3">
          Ayush Piyush Driving System
        </span>

        <h1 class="display-6 fw-semibold mb-3 section-title">
          Learn Driving with Confidence & Safety
        </h1>

        <p class="lead section-subtitle mb-4">
          Practical, structured driving lessons for beginners and advanced learners.
          We focus on control, confidence, safety, and license preparation.
        </p>

        <div class="d-flex flex-wrap gap-2">
          <a href="register_student.php" class="btn btn-primary px-4">
            Register Now
          </a>
          <a href="#services" class="btn btn-outline-primary px-4">
            View Services
          </a>
          <a href="#contact" class="btn btn-light px-4">
            Contact
          </a>
        </div>

        <!-- quick trust points -->
        <div class="d-flex flex-wrap gap-3 mt-4 small text-secondary">
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-primary border">✓</span>
            Experienced Instructors
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-primary border">✓</span>
            Beginner Friendly
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-primary border">✓</span>
            License Guidance
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="glass-card p-4 p-md-5">
          <h5 class="fw-semibold mb-2 section-title">Start Your Learning Journey</h5>
          <p class="section-subtitle mb-4">
            Register as a student to get started. Our team will contact you for schedule and classes.
          </p>

          <div class="d-grid gap-2">
            <a href="register_student.php" class="btn btn-primary">
              Register as Student
            </a>
            <a href="login.php" class="btn btn-outline-primary">
              Already Registered? Login
            </a>
          </div>

          <hr class="soft my-4">

          <div class="small section-subtitle">
            <div class="d-flex justify-content-between">
              <span>Phone</span>
              <strong class="text-dark">9852022924</strong>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <span>Location</span>
              <strong class="text-dark">Biratnagar, Munalpath</strong>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Mini Stats -->
    <div class="row g-3 mt-4">
      <div class="col-md-4">
        <div class="mini-stat p-3">
          <div class="fw-semibold section-title">Safety First</div>
          <div class="small section-subtitle">We teach rules, control & awareness.</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="mini-stat p-3">
          <div class="fw-semibold section-title">Structured Lessons</div>
          <div class="small section-subtitle">Clear steps from basics to road driving.</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="mini-stat p-3">
          <div class="fw-semibold section-title">Test Preparation</div>
          <div class="small section-subtitle">Parking, reversing & license tips.</div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ABOUT -->
<section id="about" class="py-5">
  <div class="container">
    <div class="row g-4 align-items-start">
      <div class="col-lg-6">
        <h2 class="fw-semibold mb-3 section-title">About Our Driving School</h2>
        <p class="section-subtitle mb-3">
          <strong class="text-dark">Ayush Piyush Driving System</strong> helps students become confident and responsible drivers.
        </p>
        <p class="section-subtitle mb-0">
          We provide quality training with personal attention, focusing on safety, discipline, and road awareness.
        </p>
      </div>

      <div class="col-lg-6">
        <div class="glass-card p-4">
          <h6 class="fw-semibold mb-3 section-title">We Focus On</h6>
          <div class="row g-2">
            <div class="col-12 col-md-6"><div class="section-subtitle">• Traffic rules & road safety</div></div>
            <div class="col-12 col-md-6"><div class="section-subtitle">• Vehicle control & handling</div></div>
            <div class="col-12 col-md-6"><div class="section-subtitle">• Parking & reversing</div></div>
            <div class="col-12 col-md-6"><div class="section-subtitle">• Confidence building</div></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- SERVICES -->
<section id="services" class="py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="fw-semibold section-title mb-1">Our Services</h2>
      <p class="section-subtitle mb-0">Simple, safe, and effective driving lessons</p>
    </div>

    <div class="row g-3">
      <div class="col-md-3">
        <div class="glass-card feature-card p-4 h-100">
          <div class="fw-semibold section-title mb-1">Beginner Training</div>
          <div class="small section-subtitle">Start from basics with step-by-step guidance.</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="glass-card feature-card p-4 h-100">
          <div class="fw-semibold section-title mb-1">License Preparation</div>
          <div class="small section-subtitle">Test pattern practice + tips for success.</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="glass-card feature-card p-4 h-100">
          <div class="fw-semibold section-title mb-1">Confidence Driving</div>
          <div class="small section-subtitle">Traffic handling, awareness, safe driving habits.</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="glass-card feature-card p-4 h-100">
          <div class="fw-semibold section-title mb-1">Parking Practice</div>
          <div class="small section-subtitle">Reverse, parallel, and tight-space parking skills.</div>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <a href="register_student.php" class="btn btn-primary px-5">Register Now</a>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="py-5">
  <div class="container">
    <div class="glass-card p-4 p-md-5">
      <div class="text-center mb-4">
        <h2 class="fw-semibold section-title mb-1">Frequently Asked Questions</h2>
        <p class="section-subtitle mb-0">Quick answers for new students</p>
      </div>

      <div class="accordion" id="faqAcc">
        <div class="accordion-item">
          <h2 class="accordion-header" id="q1">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1">
              Can students register by themselves?
            </button>
          </h2>
          <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faqAcc">
            <div class="accordion-body section-subtitle">
              Yes. Use the <strong>Register Now</strong> button and fill in your details. Our team will contact you.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="q2">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">
              Can admin also register students?
            </button>
          </h2>
          <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
            <div class="accordion-body section-subtitle">
              Yes. Admin can register students from the admin panel, but students can also register themselves.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="q3">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">
              How do I contact the driving school?
            </button>
          </h2>
          <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
            <div class="accordion-body section-subtitle">
              Call <strong>9852022924</strong> or visit <strong>Biratnagar, Munalpath</strong>.
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CONTACT -->
<section id="contact" class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-6">
        <h2 class="fw-semibold mb-3 section-title">Contact Information</h2>
        <p class="section-subtitle mb-4">Reach out to us for enrollment and inquiries.</p>

        <div class="glass-card p-4">
          <div class="mb-3">
            <div class="text-muted small">Driving School</div>
            <div class="fw-semibold section-title">Ayush Piyush Driving System</div>
          </div>
          <div class="mb-3">
            <div class="text-muted small">Phone</div>
            <div class="fw-semibold section-title">9852022924</div>
          </div>
          <div>
            <div class="text-muted small">Location</div>
            <div class="fw-semibold section-title">Biratnagar, Munalpath</div>
          </div>

          <div class="d-grid mt-3">
            <a href="register_student.php" class="btn btn-primary">Register Now</a>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="glass-card p-4">
          <h6 class="fw-semibold mb-3 section-title">Send Us a Message</h6>

          <form>
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" placeholder="Your name">
            </div>

            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input type="text" class="form-control" placeholder="Your contact number">
            </div>

            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea class="form-control" rows="4" placeholder="Write your message..."></textarea>
            </div>

            <button type="button" class="btn btn-primary w-100">Submit</button>

            <p class="text-muted small mt-2 mb-0">
              (Backend connection can be added later)
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
