
# asog-website
A website for the CSPC ASOG TBI

# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Google Login for Admin

1. Install dependencies:
   - Run `composer install` (or `composer update` if already installed).
2. In Google Cloud Console:
   - Create an OAuth 2.0 Client ID (Web application).
   - Add Authorized redirect URI:
     - `http://localhost:8080/asog-admin/google/callback`
     - `https://asogtbi.com/asog-admin/google/callback`
3. In your `.env`, set:
   - `googleOAuthClientId="YOUR_GOOGLE_CLIENT_ID"`
   - `googleOAuthClientSecret="YOUR_GOOGLE_CLIENT_SECRET"`
   - Optional domain restriction (comma-separated):
     - `googleOAuthAllowedDomains="gmail.com,example.edu.ph"`
   - Use `app.baseURL = 'http://localhost:8080/'` for local testing and `app.baseURL = 'https://asogtbi.com/'` on Hostinger.
4. Authorization rule:
   - Only Google accounts with verified email that already exist as active records in the `admins` table can sign in.

## Google Admin Authorization Schema

The `admins` table now supports explicit Google account linking:

- `googleEmail` - the Google account email allowed to sign in.
- `googleSub` - the stable Google account identifier returned by OAuth.

For existing installs, run the new migration and then set these values for the admin row you want to authorize.

### Manage Admin Google Authorization

#### Via Admin Dashboard
1. Go to **Admins** menu in the admin panel.
2. Click **Edit** on an admin account.
3. Enter the Google email address and optionally the Google Sub ID.
4. Save changes.

#### Via Direct SQL

**Authorize an admin to use Google OAuth:**
```sql
UPDATE admins 
SET googleEmail = 'admin@gmail.com' 
WHERE email = 'admin@yourdomain.com';
```

**Authorize with both email and stable Google Sub ID (more secure):**
```sql
UPDATE admins 
SET 
  googleEmail = 'admin@gmail.com',
  googleSub = '1234567890123456789'
WHERE email = 'admin@yourdomain.com';
```

**Remove Google authorization from an admin:**
```sql
UPDATE admins 
SET googleEmail = NULL, googleSub = NULL 
WHERE email = 'admin@yourdomain.com';
```

**List all admins and their Google authorization status:**
```sql
SELECT id, fullName, email, googleEmail, googleSub, isActive, lastLoginAt 
FROM admins 
ORDER BY createdAt DESC;
```

---

# User Manual

## Website Overview

The ASOG Technology Business Incubator (ASOG TBI) website is a comprehensive platform for startups, entrepreneurs, and stakeholders to explore incubation programs, facilities, news, and companies in the ASOG TBI ecosystem. The site is built on CodeIgniter 4 and provides both public-facing content and an admin dashboard for content management.

## For Regular Users

### Getting Started

**Base URL**: `https://asogtbi.com` (or `http://localhost:8080` for local development)

To navigate the site, use the main navigation menu at the top of every page. The menu includes links to all major sections and is accessible on both desktop and mobile devices.

---

### Navigation & Main Sections

#### 1. Home Page
- **Direct Link**: [https://asogtbi.com/](https://asogtbi.com/) or [https://asogtbi.com/home](https://asogtbi.com/home)
- **Navigation**: Click **"Home"** in the main navigation menu at the top

**What You'll See**:
- Hero section with ASOG TBI branding and tagline
- Quick overview of what ASOG TBI offers
- Featured programs section showcasing highlighted programs with images and short descriptions
- Latest news/blog posts carousel or grid
- Call-to-action buttons like:
  - **"Explore Programs"** → Click to view all programs
  - **"Meet Our Incubatees"** → Click to view companies
  - **"Learn More"** → Click to read about ASOG TBI
- Featured facilities preview
- Testimonials or success stories section
- Footer with contact information and social media links

**Interactive Elements**:
- Click on program cards to view detailed program information
- Click on news articles to read the full story
- Hover over cards to see additional information
- Click buttons to navigate to corresponding sections

---

#### 2. About Page
- **Direct Link**: [https://asogtbi.com/about](https://asogtbi.com/about)
- **Navigation**: Click **"About"** in the main navigation menu

**What You'll See**:
- **Page Title**: "About ASOG TBI"
- **Mission Statement**: Core mission of the organization
- **Vision**: Long-term goals and aspirations
- **History Section**: Background information on how ASOG TBI was founded
- **Core Values**: Key principles that guide the organization
- **Team Section**: Photos and information about leadership team members
- **Statistics/Highlights**: 
  - Number of companies incubated
  - Success rate of programs
  - Years of operation
  - Number of jobs created
- **Contact Information**: How to reach out for more information

**Navigating**:
- Scroll down to read through all sections
- Click on any embedded links for related content
- Click on team member names for more details (if available)

---

#### 3. Programs Page
- **Direct Link**: [https://asogtbi.com/programs](https://asogtbi.com/programs)
- **Navigation**: Click **"Programs"** in the main navigation menu

**What You'll See**:
- **Page Title**: "Our Programs"
- **Filter/Category Buttons**: Options to filter programs by type (optional)
- **Program Cards Grid**: Each card displays:
  - Program title
  - Program icon (SVG graphic)
  - Short description (summary text)
  - Program category badge
  - **"Learn More"** or **"Read More"** button

**Exploring Individual Programs**:
1. Click on a program card or the **"Learn More"** button
2. You'll be redirected to the program's detail page (e.g., `https://asogtbi.com/programs/accelerator`)
3. On the detail page, you'll see:
   - Full program title and featured image
   - Comprehensive description
   - Program benefits (bulleted list):
     - Mentorship opportunities
     - Funding access
     - Networking events
     - Facilities access
   - Requirements or eligibility criteria
   - Timeline or duration
   - Application link or CTA button
   - Related programs section (links to similar programs)

**Interactive Elements**:
- Hover over program cards to see animations or additional info
- Click **"Apply Now"** button to submit your application
- Click **"Back to Programs"** link to return to the programs list
- Share button to share on social media

---

#### 4. Facilities Page
- **Direct Link**: [https://asogtbi.com/facilities](https://asogtbi.com/facilities)
- **Navigation**: Click **"Facilities"** in the main navigation menu

**What You'll See**:
- **Page Title**: "Our Facilities"
- **Introduction Section**: Overview of facilities available
- **Facility Cards Grid**: Each card shows:
  - Facility name/title
  - Featured image or photo
  - Short description
  - Available amenities indicators (icons)
  - **"View Details"** or **"Learn More"** button

**Viewing Facility Details**:
1. Click on a facility card or **"View Details"** button
2. Redirected to facility page (e.g., `https://asogtbi.com/facilities/conference-room`)
3. Detailed facility page displays:
   - Facility name and high-quality images (carousel or gallery)
   - Full description of the facility
   - Amenities list:
     - Capacity (number of people)
     - Available equipment
     - Technology features
     - Special features
   - Location and directions
   - Booking information:
     - Availability
     - Contact person
     - Booking process/link
   - Facility specifications (dimensions, power outlets, etc.)
   - Photo gallery with multiple images

**Interactive Elements**:
- Click **"Book Now"** to submit a facility reservation request
- Photo gallery: Click images to enlarge or swipe through
- Click **"Contact for Booking"** to open contact form
- Print facility information using browser print function
- Share facility on social media

---

#### 5. Incubatees Page
- **Direct Link**: [https://asogtbi.com/incubatees](https://asogtbi.com/incubatees)
- **Navigation**: Click **"Incubatees"** in the main navigation menu (or **"Companies"**)

**What You'll See**:
- **Page Title**: "Our Incubatees"
- **Filter/Search Options**:
  - Filter by cohort (Cohort 1, Cohort 2, etc.)
  - Filter by industry
  - Filter by SDG alignment
  - Search bar to find companies by name
- **Company Cards Grid**: Each card displays:
  - Company logo
  - Company name
  - Founder name
  - Short description
  - Website link icon
  - **"View Profile"** button
  - SDG badges (Sustainable Development Goals the company supports)

**Viewing Company Profiles**:
1. Click on a company card or **"View Profile"** button
2. Redirected to company page (e.g., `https://asogtbi.com/incubatees/tech-startup-xyz`)
3. Company profile page shows:
   - **Company Logo**: Large display
   - **Company Name**: Official business name
   - **Founded**: Year of establishment
   - **Founder/CEO**: Names of key founders
   - **Company Description**: Full description of what they do
   - **Industry/Sector**: What industry they operate in
   - **Mission Statement**: Company's mission
   - **Products/Services**: Details of offerings
   - **SDG Alignment**: Which Sustainable Development Goals they support (with icons)
   - **Cohort Information**: Which ASOG TBI cohort they joined
   - **Team Members**: List of team members with roles
   - **Gallery**: Photos/screenshots of products or team
   - **Website Link**: **"Visit Website"** button to company's website
   - **Contact Information**: How to reach the company
   - **Related Incubatees**: Similar companies in the program

**Interactive Elements**:
- Click company logo to enlarge
- Click website link to visit company's site
- Scroll through photo gallery
- Click social media icons if available
- Click **"Back to Incubatees"** to return to listing
- Share company profile on social media

**Advanced Filtering Example**:
1. Click "Filter by Cohort" dropdown → Select "Cohort 2024"
2. Page updates to show only companies from that cohort
3. Click "Clear Filters" to reset view

---

#### 6. News Page
- **Direct Link**: [https://asogtbi.com/news](https://asogtbi.com/news)
- **Navigation**: Click **"News"** or **"Blog"** in the main navigation menu

**What You'll See**:
- **Page Title**: "Latest News"
- **Featured News Section**: 
  - Large featured article with image and headline
  - **"Read Full Story"** button
- **News Filter Buttons**: By category (announcements, updates, press releases, blog)
- **News Articles List**: Each article preview shows:
  - Featured image/thumbnail
  - Article title
  - Publication date
  - Author name
  - Short excerpt/summary (first 150 characters)
  - Category badge
  - **"Read More"** link

**Reading Full Articles**:
1. Click on article title or **"Read More"** link
2. Redirected to article page (e.g., `https://asogtbi.com/news/asog-tbi-launches-2024-cohort`)
3. Full article page displays:
   - Article title (H1 heading)
   - Publication date and author
   - Breadcrumb navigation (News > Article Title)
   - Featured image at full width
   - Full article content with:
     - Paragraphs of text
     - Inline images
     - Formatted lists
     - Quotes or highlights
     - Links to related content
   - "Published on" date and last updated date
   - Author bio section (if available)
   - Social sharing buttons:
     - **Share on Facebook**
     - **Share on Twitter/X**
     - **Share on LinkedIn**
     - **Copy Link** button
   - Related articles section (suggestions for similar content)
   - **Previous/Next Article** navigation buttons
   - Comments section (if enabled)

**Navigation Features**:
- Pagination at bottom (Page 1, 2, 3... or **"Load More"** button)
- Click on category tags to filter by topic
- Use search bar to find specific articles
- Click breadcrumb links to navigate back

---

#### 7. Organization Page
- **Direct Link**: [https://asogtbi.com/organization](https://asogtbi.com/organization)
- **Navigation**: Click **"Organization"** or **"Team"** in the main navigation menu

**What You'll See**:
- **Page Title**: "Organization Structure"
- **Leadership Section**:
  - Executive Director/CEO
  - Their photo, title, and bio
  - Contact information
- **Organizational Chart**: Visual hierarchy showing:
  - Executive level
  - Department heads
  - Team leads
  - Staff members
- **Departments Section**: Cards for each department:
  - Department name
  - Department head
  - Team members list
  - Department mission
  - Department contact info
- **Full Staff Directory**: 
  - Name
  - Position/Title
  - Department
  - Email
  - Phone (if public)
  - Photo

**Interactivity**:
- Hover over organizational chart to see tooltips with names
- Click on team member names to see full profile
- Click email addresses to open compose email
- Click phone numbers on mobile to call directly
- Filter staff by department

---

#### 8. Contact Page
- **Direct Link**: [https://asogtbi.com/contact](https://asogtbi.com/contact)
- **Navigation**: Click **"Contact"** or **"Get in Touch"** in the main navigation menu

**What You'll See**:
- **Page Title**: "Contact Us"
- **Contact Information Section**:
  - **Address**: Full physical address with Google Maps embed
  - **Phone**: One or multiple phone numbers with clickable links
  - **Email**: Email addresses (clickable links)
  - **Hours of Operation**: Business hours display
  - **Social Media Links**: Facebook, Twitter, LinkedIn, Instagram icons/links

**Google Maps Integration**:
- Embedded map showing ASOG TBI location
- Click map to view in full Google Maps
- Get directions link

**Contact Form**:
- Fill in form with your information:
  - **Name** (required field) - text input
  - **Email** (required field) - email input
  - **Subject** (optional) - dropdown or text input
  - **Message** (required field) - large text area
  - **Phone** (optional) - phone number input
  - **Company** (optional) - text input
  - **Inquiry Type** (optional) - dropdown:
    - Program Information
    - Facility Booking
    - Partnership
    - General Inquiry
    - Other
- **CAPTCHA**: Verify you're human
- **"Submit"** button
- **"Clear"** button to reset form

**After Submission**:
- Success message displays: "Thank you! Your message has been sent."
- Confirmation email sent to the address you provided
- Staff will respond within 24-48 hours
- Optional: Option to view **"Frequently Asked Questions"** after submission

---

#### 9. Games Page
- **Direct Link**: [https://asogtbi.com/games](https://asogtbi.com/games)
- **Navigation**: Click **"Games"** or **"Experiences"** in the main navigation menu

**What You'll See**:
- **Page Title**: "Interactive Experiences"
- **Game/Experience Cards**: Each displays:
  - Game title
  - Description
  - Preview image
  - Difficulty level or age recommendation
  - **"Play Now"** button

**Playing Games**:
1. Click **"Play Now"** button
2. Game loads in a new section or page
3. Interactive experience begins with:
   - Game instructions
   - Start button
   - Control instructions
   - Progress indicator
4. Complete the game/experience
5. View results or score
6. **Share Results** button to share on social media
7. **Play Again** or **Back to Games** option

**Examples of Experiences**:
- 3D interactive environments (like ALTITUDE 3D experience)
- Startup simulation games
- Educational quizzes
- Memory/matching games
- Interactive tours of facilities

**Interactive Features**:
- Mouse/keyboard controls (described on screen)
- Mobile-friendly touch controls
- Fullscreen option
- Pause/resume functionality
- Sound toggle
- Performance feedback/scoring

---

### Additional Navigation Features

#### Top Navigation Bar
- **Logo**: Click ASOG TBI logo to return to home page
- **Main Menu**: Hamburger menu (☰) on mobile devices
- **Search Icon**: Click to search the entire site
- **Dark/Light Mode** (if available): Toggle theme

#### Mobile Navigation
- **Hamburger Menu**: Opens slide-out or dropdown menu
- **Back Button**: Navigate to previous page
- **Search**: Mobile-optimized search bar
- **Mobile-Friendly**: All pages stack vertically for easy scrolling

#### Footer Navigation
Present on every page:
- **Quick Links**: Home, Programs, Facilities, News
- **About**: About us, Organization, Contact
- **Social Media**: Links to Facebook, Twitter, LinkedIn, Instagram
- **Legal**: Privacy Policy, Terms of Service, Sitemap
- **Contact Info**: Email, phone, address
- **Newsletter Signup** (optional): Email subscription form

**Click footer links to navigate to those sections instantly**

---

### Browsing & Search Features

#### Site Search
- **How to Access**: Click search icon (🔍) in top navigation
- **Search Bar**: Type keywords to find content
- **Results Display**:
  - Programs
  - News articles
  - Incubatees
  - Facilities
  - Other pages
- **Filter Results**: By content type
- **Click Results**: Direct navigation to matching page

#### Filters & Categories
- **Program Filters**: By type, duration, target audience
- **News Filters**: By category, date range, author
- **Company Filters**: By cohort, industry, SDG goals
- **Facility Filters**: By type, capacity, amenities

**How to Use Filters**:
1. Navigate to a listing page (Programs, News, Facilities, Incubatees)
2. Click filter button or dropdown
3. Select filter criteria
4. Results update automatically
5. Click "Clear Filters" to reset

#### Sorting Options
- **Sort by**: 
  - Date (newest first or oldest first)
  - Alphabetical (A-Z or Z-A)
  - Featured (important items first)
  - Popularity (most viewed)
- **Items per page**: Choose how many items to display

#### Pagination
- **Previous/Next Buttons**: Navigate between pages
- **Page Numbers**: Jump directly to a page
- **Load More Button** (Alternative): Click to load additional items

#### Social Sharing
**Share Articles/Content**:
1. Click **Share** button on page
2. Options appear:
   - **Facebook**: Click to share on Facebook with preview
   - **Twitter/X**: Click to share on Twitter with preview
   - **LinkedIn**: Click to share on LinkedIn
   - **Copy Link**: Copy page URL to clipboard
   - **Email**: Share via email
3. Window opens to share on selected platform
4. Add your own message (optional)
5. Click "Post" or "Share" to publish

---

### Responsive Design & Device Support

**Desktop Experience**:
- Full-width navigation bar
- Side-by-side columns
- Hover effects on buttons and links
- Large images and readable text

**Tablet Experience**:
- Adaptive layout adjusting to screen size
- Touch-friendly button sizes
- Navigation adjusts as needed
- Readable content with proper spacing

**Mobile Experience**:
- Hamburger menu (☰) for navigation
- Single-column layout
- Large touch targets for buttons
- Optimized images for mobile
- Swipeable galleries and carousels
- **Portrait and Landscape** modes supported

**To Test Responsive Design**:
1. Open website on different devices
2. Or use browser developer tools:
   - Press **F12** to open DevTools
   - Click responsive design mode (Ctrl+Shift+M)
   - Select device type from dropdown
   - See how page adapts to different screen sizes

---

### Accessibility Features

**For All Users**:
- **Keyboard Navigation**: Tab through all interactive elements using keyboard
- **Screen Reader Support**: Compatible with JAWS, NVDA, VoiceOver
- **High Contrast Mode**: Works with system high contrast settings
- **Alt Text**: All images have descriptive alt text for screen readers
- **Readable Fonts**: Clear, readable font selections
- **Color Contrast**: Text is readable against background colors

**Keyboard Shortcuts**:
- **Tab**: Move to next interactive element
- **Shift+Tab**: Move to previous interactive element
- **Enter**: Activate buttons and links
- **Space**: Interact with checkboxes and radio buttons
- **Esc**: Close menus or modals

---

### SEO & Meta Information

Each page includes:
- **Page Title**: Appears in browser tab and search results
- **Meta Description**: Summary appears in search results
- **Open Graph Tags**: Custom preview when sharing on social media
- **Canonical URL**: Prevents duplicate content issues
- **Structured Data**: Helps search engines understand content

**What This Means for You**:
- Better search visibility
- Rich previews when sharing
- Consistent information across platforms
- Improved discoverability

---

## For Administrators

### Admin Panel Access

#### Accessing the Admin Dashboard

**Admin URL**: `https://asogtbi.com/asog-admin` (or `http://localhost:8080/asog-admin` for local development)

**Step-by-Step Login**:

1. **Navigate to Admin Login**:
   - Open your web browser
   - Visit: `https://asogtbi.com/asog-admin`
   - You will see the admin login page

2. **Login Page Elements**:
   - **ASOG TBI Logo**: At the top of the page
   - **Page Title**: "Admin Dashboard Sign In"
   - **Sign In Options**: 
     - **"Sign in with Google"** button (primary method)
     - Username/password fields (if traditional login is enabled)

3. **Click "Sign in with Google"**:
   - Button redirects you to Google login page
   - Enter your Google email address (Gmail account)
   - Enter your Google password
   - You may be asked to approve access

4. **After Google Authentication**:
   - You are redirected back to ASOG TBI admin dashboard
   - If this is your first login, you may be asked for additional information
   - Dashboard loads with your admin menu

5. **Admin Dashboard Home Page**:
   - **Welcome Message**: "Welcome, [Your Name]"
   - **Dashboard Overview**: 
     - Quick statistics
     - Recent activity
     - Pending tasks or reviews
   - **Left Sidebar Menu**: Main navigation for admin functions
   - **Top Right**: Your profile icon with dropdown menu:
     - View Profile
     - Settings
     - Sign Out
   - **Breadcrumb Navigation**: Shows current location (Dashboard > Section)

#### Authorization Requirements

**To Successfully Login**:
1. Your Google email address must be registered in the `admins` table
2. Your admin account must have `isActive = 1` (account must be enabled)
3. Your account must have an appropriate `role` assigned:
   - `admin` - Full access to all features
   - `editor` - Can manage content but not admin users
   - `viewer` - Read-only access
4. Google OAuth credentials must be configured in `.env` file on the server

**If You Can't Login**:
- Check that your Google email matches the email in the system
- Verify with your system administrator that your account is active
- Clear browser cookies and try again
- Use private/incognito mode to rule out browser cache issues

**Authorization in Database**:
- Your account is authorized when:
  - `googleEmail` field = your Google account email
  - `isActive` field = 1 (enabled)
  - Your account role allows the action you're trying to perform

---

### Admin Dashboard Layout

#### Left Sidebar Menu

The main navigation menu appears on the left side of all admin pages:

**Main Sections** (in typical order):
1. **Dashboard** - Home/overview (click to return)
2. **Posts** - News and blog article management
3. **Programs** - Program listing management
4. **Facilities** - Facilities management
5. **Incubatees** - Company/startup profiles
6. **Admins** - User account management
7. **Settings** - System configuration (if available)

**Common Sidebar Features**:
- Click any section to navigate
- Section highlights when active
- Sub-menus expand when available
- Search box to find content
- Collapse/expand menu button (☰) on mobile

#### Top Navigation Bar

**Left Side**:
- **Hamburger Menu** (☰): Expand/collapse sidebar
- **ASOG TBI Logo**: Click to return to dashboard home

**Right Side**:
- **Search Icon**: Global search across all content
- **Notifications Bell**: Shows pending reviews or updates
- **Help Icon**: Link to documentation or support
- **Profile Icon**: Your avatar or initials
  - Click to open dropdown:
    - **Profile Settings** - Edit your information
    - **Change Password** - Update your password
    - **Email Preferences** - Notification settings
    - **Sign Out** - Logout and end session

#### Content Area

**Main Content Section** (middle/right area):
- **Page Title**: Current page/section name
- **Breadcrumb Navigation**: Shows your location
- **Action Buttons**: 
  - **+ Create New** or **+ Add** (top right)
  - **Edit** (on existing items)
  - **Delete** (on existing items)
  - **Save** / **Cancel** (when editing)
- **Content/Form Fields**: Depends on section
- **Status Messages**: Notifications of actions taken

---

### 1. News & Posts Management

**Access Path**: Sidebar → **Posts** → (or Dashboard → click "Posts" card)

**Posts List Page** (`/asog-admin/posts` or similar):

**What You'll See**:
- **Page Title**: "Blog Posts" or "News Articles"
- **Action Buttons**:
  - **+ Create New Post** (top right, green/blue button)
  - **Export** (export posts to CSV)
- **Filter/Search Section**:
  - **Search Box**: Type title or keywords to find posts
  - **Filter by Status**: Draft, Published, Scheduled, Archived
  - **Filter by Category**: News, Blog, Announcement, Updates
  - **Date Range Picker**: Filter by publish date
  - **Author Filter**: Show posts by specific author
  - **"Apply Filters"** button
  - **"Clear Filters"** button

- **Posts Table** with columns:
  - **Checkbox**: Select multiple posts (for bulk actions)
  - **Title**: Article headline
  - **Status Badge**: Draft (gray), Published (green), Scheduled (blue)
  - **Category**: Label like "News" or "Blog"
  - **Author**: Who created the post
  - **Published Date**: When it went live
  - **Views Count**: How many people viewed it
  - **Actions Dropdown**: 
    - **Edit** - Opens edit form
    - **View** - See live version
    - **Duplicate** - Copy this post
    - **Delete** - Remove permanently
    - **Archive** - Hide but keep in system

**Pagination**:
- Shows "Showing 1-10 of 45 posts"
- Page numbers or "Previous/Next" buttons
- Items per page dropdown (10, 25, 50, 100)

**Bulk Actions** (when items are selected):
- **Publish Selected**: Make multiple posts public
- **Change Category**: Apply category to selected
- **Delete Selected**: Remove multiple posts
- **Move to Archive**: Archive selected posts

---

#### Creating a New Post

**Step 1**: Click **+ Create New Post** button
- Page redirects to `/asog-admin/posts/create`
- Blank form with empty fields loads

**Step 2**: **Post Creation Form** displays with:

**Basic Information Section**:
- **Title** (required field, text input):
  - Placeholder: "Enter post title"
  - Character count: Shows how many characters used
  - Slug auto-generates below as you type

- **Slug** (required field, text input):
  - Auto-populated from title (e.g., "my-first-post")
  - Editable for custom URLs
  - Used in URL: `/news/my-first-post`

- **Short Description** (optional, text area):
  - Placeholder: "Brief summary for listings"
  - 500 character limit
  - Shows character counter
  - Appears in article listings

**Content Section**:
- **Content** (required field, rich text editor):
  - WYSIWYG editor with toolbar:
    - Text formatting: Bold (B), Italic (I), Underline (U)
    - Heading levels: H1, H2, H3
    - Lists: Unordered (•) and Ordered (1, 2, 3)
    - Quote block
    - Code block
    - Link insertion
    - Image upload
    - Text alignment: Left, Center, Right
    - Undo/Redo buttons
  - Full text editor area for writing content
  - Character count or word count display
  - Preview button to see formatted result

**Media Section**:
- **Featured Image** (optional):
  - **Upload Button**: Click to browse and select image
  - **Image Preview**: Shows uploaded image thumbnail
  - **Remove Button**: Delete the selected image
  - **Alt Text** (required if image uploaded):
    - Text input for image description
    - Used for SEO and accessibility
  - **Image Size Recommendations**: "Recommended 1200x600px"
  - **Upload Progress**: Shows percentage when uploading

**Publishing Options**:
- **Category** (required dropdown):
  - Options: "News", "Blog", "Announcement", "Press Release", "Update"
  - Select from dropdown

- **Author** (auto-filled or dropdown):
  - Shows current logged-in user
  - Can change to another admin if permissions allow

- **Status** (required radio buttons):
  - ( ) Draft - Keep private
  - ( ) Scheduled - Publish at specific time
  - ( ) Published - Make public immediately
  - ( ) Archived - Hide from public

- **Publish Date & Time** (required):
  - **Date Picker**: Click to select date from calendar
  - **Time Picker**: Select hours and minutes
  - Shows: "Will be published on [date] at [time]"

- **Published Date** (if editing):
  - Shows when article was first published

- **Featured** (optional checkbox):
  - ☐ Feature this post on homepage
  - When checked, appears in featured section

**Additional Settings**:
- **Sort Order** (numeric field):
  - Enter number (lower numbers appear first)
  - Determines display priority
  - Example: 1, 2, 3 (1 displays first)

- **SEO Section** (expandable):
  - **Meta Description** (optional):
    - 160 character limit
    - Appears in search results
    - Shows character counter
  - **Meta Keywords** (optional):
    - Tags for search engines
    - Separated by commas
  - **Canonical URL** (optional):
    - Advanced SEO setting
    - Usually auto-filled

- **Tags/Categories** (optional):
  - Add multiple tags
  - Type and press Enter
  - Click "X" to remove tag

**Step 3**: **Action Buttons** (usually at bottom):
- **Save Draft** (gray button):
  - Saves post without publishing
  - Can edit later
  - Message: "Post saved as draft"
  
- **Schedule** (if date is in future):
  - Saves and schedules publication
  - Message: "Post scheduled for [date]"

- **Publish** (green/blue button):
  - Immediately publishes post
  - Makes visible on website
  - Message: "Post published successfully"
  - Provides link to view on website

- **Cancel** (gray button):
  - Discards changes
  - Returns to posts list
  - Prompts: "Are you sure? Unsaved changes will be lost"

- **Preview** (optional):
  - Shows how post looks on website
  - Opens in new tab or modal
  - Preview updates as you edit

**Step 4**: **After Publishing**:
- Confirmation message: "Your post has been published!"
- Link to **"View on Website"** - Opens published page
- Option to **"Edit"** or **"Create Another"**
- Automatically returns to posts list after a few seconds
- New post appears in posts table with "Published" status

---

#### Editing an Existing Post

1. Navigate to **Posts** section
2. Find the post in the table
3. Click **Edit** button or click the title itself
4. Post edit form opens with all current information pre-filled
5. Make your changes to any fields
6. Click **Update** or **Save Changes** button
7. Success message: "Post updated successfully"
8. Changes appear immediately on website

**Common Edits**:
- **Change Status**: Change from Draft to Published
- **Feature/Unfeature**: Toggle featured status
- **Update Content**: Edit text, images, formatting
- **Reschedule**: Change publish date and time
- **Change Category**: Move to different category
- **Update Slug**: Change URL (will redirect old URL)

---

#### Viewing Published Post

1. In posts list, click **View** icon/button next to post
2. Opens published post in new tab or window
3. You see the post as website visitors see it:
   - Title
   - Featured image
   - Publication date and author
   - Full content
   - Social sharing buttons
   - Comments (if enabled)

---

### 2. Programs Management

**Access Path**: Sidebar → **Programs** → (or Dashboard → click "Programs" card)

**Programs List Page** (`/asog-admin/programs`):

**What You'll See**:
- **Page Title**: "Programs"
- **+ Create New Program** button (top right)
- **Filter/Search**:
  - Search by program title
  - Filter by published status
  - Filter by sort order range
  - **Apply** / **Clear** buttons

- **Programs Table**:
  - **Program Title**: Name of the program
  - **Status**: Published (green) or Draft (gray)
  - **Sort Order**: Display priority number
  - **Created Date**: When program was added
  - **Actions Dropdown**: Edit, View, Delete, Duplicate

---

#### Creating a New Program

**Step 1**: Click **+ Create New Program** button

**Program Creation Form**:

**Basic Information**:
- **Title** (required):
  - Program name
  - Slug auto-generates

- **Slug** (required):
  - URL identifier
  - Editable for custom URLs

- **Short Description** (optional):
  - Brief summary for listings
  - 300-500 character limit

**Content**:
- **Content** (required, rich text editor):
  - Full program description
  - Benefits list
  - Requirements
  - Timeline
  - Application process
  - Same formatting tools as posts

**Visual Elements**:
- **Program Icon** (optional):
  - Upload SVG file (recommended)
  - Or upload PNG/JPG image
  - Icon dimensions: 100x100px
  - Used in program listings

- **Featured Image** (optional):
  - Large image for program detail page
  - Recommended size: 1200x400px
  - With alt text field

**Publishing**:
- **Status**: Draft or Published
- **Sort Order**: Number for display priority
- **Published Date/Time**: When to publish

**Step 2**: Click **Create Program** button
- Confirmation message shows
- Redirects to programs list
- New program appears with Published/Draft status

---

#### Editing a Program

1. Find program in list
2. Click **Edit** button
3. Form opens with current data
4. Modify fields as needed
5. Click **Update Program**
6. Changes display on website immediately

---

### 3. Facilities Management

**Access Path**: Sidebar → **Facilities**

**Facilities List Page**:

**What You'll See**:
- **Page Title**: "Facilities"
- **+ Add New Facility** button
- **Facilities Table** with:
  - Facility name
  - Status (Published/Draft)
  - Sort order
  - Actions (Edit, Delete, View)

---

#### Creating a New Facility

**Facility Form**:

**Basic Information**:
- **Facility Name** (required):
  - Official name of facility
  - Example: "Conference Room A"

- **Slug** (required):
  - URL identifier
  - Example: "conference-room-a"

- **Short Description** (optional):
  - Brief overview for listings
  - Character limit

**Detailed Information**:
- **Content** (required, rich text editor):
  - Full facility description
  - Amenities list
  - Capacity information
  - Technical specifications
  - Booking instructions

**Visual Elements**:
- **Featured Image** (optional):
  - Photo of facility
  - 1200x600px recommended

- **Image Gallery** (optional):
  - Multiple facility photos
  - Upload and arrange in order
  - Drag to reorder

**Specifications**:
- **Capacity** (optional, number):
  - Maximum number of people
  - Example: 50 people

- **Available Amenities** (optional, checkboxes):
  - WiFi
  - Projector
  - Whiteboard
  - Meeting Table
  - Air Conditioning
  - Power Outlets
  - Parking
  - Catering Available

**Publishing**:
- **Status**: Published or Draft
- **Sort Order**: Priority number
- **Published Date**: When to publish

**Step**: Click **Save Facility** button
- Facility created and added to list
- Appears on website in facilities section

---

### 4. Incubatees Management

**Access Path**: Sidebar → **Incubatees**

**Incubatees List Page**:

**What You'll See**:
- **Page Title**: "Incubatees"
- **+ Add New Incubatee** button
- **Filter/Search**:
  - Search by company name or founder
  - Filter by cohort
  - Filter by published status

- **Incubatees Table**:
  - Company name
  - Founder name
  - Cohort
  - Status
  - Created date
  - Actions

---

#### Creating a New Incubatee

**Step 1**: Click **+ Add New Incubatee** button

**Incubatee Creation Form**:

**Company Information**:
- **Company Name** (required):
  - Official business name

- **Founder Name** (optional):
  - Name of primary founder

- **Slug** (required):
  - URL identifier
  - Auto-generated from company name

- **Short Description** (optional):
  - Brief company summary
  - 300 characters max

**Detailed Information**:
- **Content** (required, rich text editor):
  - Full company description
  - Mission statement
  - Products/services
  - Team information
  - Company achievements

**Company Details**:
- **Cohort** (optional, dropdown):
  - Select cohort number
  - Example: "Cohort 2024", "Cohort 2025"

- **Website URL** (optional):
  - Full URL of company website
  - Example: "https://www.company.com"
  - Clickable link on website

- **Team Members** (optional, text area):
  - List team members
  - Include names and roles
  - Format: "Name - Role, Name - Role"

**Visual Elements**:
- **Company Logo** (optional):
  - Upload company logo
  - Square format recommended (500x500px)
  - Used in company listings

- **Featured Image** (optional):
  - Company photo or product image
  - 1200x600px recommended

- **Image Gallery** (optional):
  - Multiple company/product photos
  - Drag to reorder

**SDG Alignment**:
- **SDG Numbers** (optional):
  - List of SDG goals company supports
  - Checkboxes or multi-select:
    - No Poverty
    - Zero Hunger
    - Good Health
    - Quality Education
    - Gender Equality
    - Clean Water
    - etc.
  - Shows SDG badges on website

**Publishing**:
- **Status**: Published or Draft
- **Sort Order**: Priority number
- **Published**: When to publish

**Step 2**: Click **Save Incubatee** button
- Company profile created
- Added to incubatees directory
- Appears on website

---

#### Editing an Incubatee

1. Find company in Incubatees list
2. Click **Edit**
3. Form opens with current information
4. Update any fields
5. Click **Update Incubatee**
6. Changes appear on website

**Common Edits**:
- Update company achievements
- Add new team members
- Change website URL
- Update SDG alignment
- Change cohort
- Feature/unfeature

---

### 5. Admin User Management

**Access Path**: Sidebar → **Admins** → (or Settings → User Management)

**Admins List Page** (`/asog-admin/admins`):

**What You'll See**:
- **Page Title**: "Admin Users"
- **+ Add New Admin** button
- **Admins Table** with columns:
  - Full Name
  - Email
  - Role (admin, editor, viewer)
  - Status (Active/Inactive)
  - Last Login date/time
  - Actions (Edit, Deactivate, Delete)

**Filtering**:
- Search by name or email
- Filter by role
- Filter by active status

---

#### Creating a New Admin User

**Step 1**: Click **+ Add New Admin** button
- Admin creation form opens

**Admin Information Form**:

**Basic Information**:
- **Full Name** (required):
  - Admin's full name
  - Example: "John Smith"

- **Email** (required):
  - Email address for login
  - Must be unique
  - Example: "john@yourdomain.com"

- **Password** (required):
  - Set initial password
  - Shown in plain text initially
  - Admin should change on first login
  - Password strength indicator shows (Weak/Fair/Strong)
  - Minimum 8 characters recommended

- **Role** (required, dropdown):
  - **Admin**: Full access to all features
  - **Editor**: Can manage content (news, programs, facilities, incubatees) but not admin users
  - **Viewer**: Read-only access, cannot make changes

**Google OAuth Setup** (optional but recommended):

- **Google Email** (optional):
  - The Google account email for this admin
  - Example: "john@gmail.com"
  - If set, admin can sign in with this Google account

- **Google Sub** (optional):
  - Stable Google ID for enhanced security
  - Find this in Google Account settings
  - More secure than just email

**Account Status**:
- ☑ **Active**: Account is enabled (checked by default)
  - Unchecked = account disabled, can't login

**Step 2**: Click **Create Admin** button
- New admin user created
- Added to admins table with "Active" status
- Email notification sent to new admin (if configured)
- Message: "Admin user created successfully"

**Step 3**: New Admin First Login:
- New admin receives welcome email (optional)
- Navigates to `/asog-admin`
- Clicks "Sign in with Google" if Google OAuth is configured
- Or uses email/password if traditional login is configured
- Should change password on first login

---

#### Editing an Admin

1. Find admin in list
2. Click **Edit** on that admin row
3. Admin edit form opens with current information:
   - Full Name (editable)
   - Email (editable)
   - Role (can change)
   - Google Email (can add/update)
   - Google Sub (can add/update)
   - Active status (can toggle)
4. Make changes as needed
5. Click **Update Admin**
6. Changes take effect immediately

**Common Admin Changes**:
- **Promote/Demote**: Change role from Editor to Admin
- **Disable Account**: Uncheck "Active" to prevent login
- **Add Google OAuth**: Fill in Google Email for OAuth login
- **Change Permissions**: Adjust what admin can access

---

#### Deactivating an Admin

**Method 1**: Quick Deactivate
- Find admin in list
- Click **Deactivate** button
- Confirmation dialog: "Are you sure you want to deactivate [Name]?"
- Click **Yes, Deactivate**
- Status changes to "Inactive"
- Admin cannot login anymore

**Method 2**: Edit and Uncheck Active
1. Click **Edit** on admin
2. Uncheck the "Active" checkbox
3. Click **Update**
4. Admin is deactivated

---

#### Deleting an Admin

**⚠️ Warning: Deletion is permanent**

1. Find admin in list
2. Click **Delete** button
3. Confirmation dialog: "Permanently delete [Name]? This cannot be undone."
4. Click **Yes, Delete**
5. Admin record removed from system
6. Cannot be recovered

**Alternative**: Deactivate instead of delete (safer option)

---

#### Resetting an Admin Password

If an admin forgets their password:

**As an Admin with User Management Access**:
1. Go to **Admins** section
2. Find the admin who needs reset
3. Click **Edit**
4. In the **Password** field, enter a temporary password
5. Click **Update**
6. Send temporary password to admin via secure email
7. Admin logs in with temporary password
8. System prompts to change password
9. Admin sets their own new password

**For Self-Service** (if enabled):
- Admin clicks **"Forgot Password"** on login page
- Enters email address
- Receives password reset link via email
- Clicks link to reset password
- Sets new password
- Logs in with new password

---

#### Viewing Admin Activity/Last Login

In **Admins** list, the **Last Login** column shows:
- **Date**: Date of last login
- **Time**: Time of last access
- **"Never"**: If admin has never logged in

**This helps you**:
- Identify inactive admins
- Monitor admin activity
- Deactivate dormant accounts for security

---

### Database Management

#### Backup & Restore

**Creating a Database Backup**:

**Using Command Line**:
```bash
mysqldump -u username -p database_name > backup.sql
```
- You'll be prompted for password
- Creates SQL file with entire database structure and data
- File is ready to restore or archive

**Using Hostinger Hosting Panel**:
1. Log into Hostinger control panel
2. Navigate to MySQL Databases
3. Find your database
4. Click **Manage**
5. Click **Backup** or **Export**
6. Select backup date
7. Download SQL file
8. Keep file in secure location

**Automating Backups**:
- Use hosting provider's automated backup feature
- Schedule daily or weekly backups
- Store backups in multiple locations
- Test restore process periodically

---

#### Admin SQL Commands

**Add a New Admin** (via SQL):
```sql
INSERT INTO admins (fullName, email, password, role, isActive, createdAt, updatedAt)
VALUES ('John Doe', 'john@example.com', SHA2('securepassword', 256), 'admin', 1, NOW(), NOW());
```
- Replace values in quotes with actual information
- Password is hashed for security
- isActive = 1 means account is enabled
- Run in database management tool like phpMyAdmin

**Update Admin Password**:
```sql
UPDATE admins 
SET password = SHA2('newpassword', 256), updatedAt = NOW() 
WHERE email = 'admin@yourdomain.com';
```
- Changes password for specified admin
- Password is automatically hashed
- Run when admin forgets password (use temporary password)

**Deactivate an Admin Account**:
```sql
UPDATE admins 
SET isActive = 0, updatedAt = NOW() 
WHERE email = 'admin@yourdomain.com';
```
- Sets isActive = 0 (disabled)
- Admin cannot login but record remains
- Data preserved for audit trail

**Reactivate an Admin Account**:
```sql
UPDATE admins 
SET isActive = 1, updatedAt = NOW() 
WHERE email = 'admin@yourdomain.com';
```
- Sets isActive = 1 (enabled)
- Admin can login again

**Delete an Admin Account**:
```sql
DELETE FROM admins 
WHERE email = 'admin@yourdomain.com';
```
- Permanently removes admin record
- ⚠️ Cannot be undone
- Consider deactivating instead

**List All Admins and Status**:
```sql
SELECT id, fullName, email, googleEmail, role, isActive, lastLoginAt, createdAt 
FROM admins 
ORDER BY createdAt DESC;
```
- Shows all admin users with their details
- isActive = 1 (active) or 0 (inactive)
- lastLoginAt shows when they last accessed
- Use to audit admin accounts

**Add Google OAuth to an Existing Admin**:
```sql
UPDATE admins 
SET 
  googleEmail = 'john@gmail.com',
  googleSub = '1234567890123456789',
  updatedAt = NOW()
WHERE email = 'john@yourdomain.com';
```
- Enables Google login for existing admin
- Find googleSub in Google Account settings
- Admin can now login with Google

**Remove Google OAuth from an Admin**:
```sql
UPDATE admins 
SET googleEmail = NULL, googleSub = NULL, updatedAt = NOW() 
WHERE email = 'admin@yourdomain.com';
```
- Disables Google login for this admin
- Admin must use traditional login method

**Find Admin by Email**:
```sql
SELECT * FROM admins WHERE email = 'admin@yourdomain.com';
```
- Shows all details for one admin
- Use to verify admin information

**Access Database via SQL Client**:

**Using phpMyAdmin** (typical in hosting panels):
1. Log into hosting control panel (Hostinger, etc.)
2. Find **phpMyAdmin** or **MySQL Databases**
3. Select your database
4. Click **SQL** tab
5. Paste SQL command
6. Click **Execute** or **Go**
7. Results display or confirmation message shows

**Using MySQL Command Line** (terminal/PowerShell):
```bash
mysql -h hostname -u username -p databasename
```
- Enter password when prompted
- Type SQL commands
- Type `EXIT;` to quit

---

### Content Management Best Practices

**1. Publish Regularly**:
- Post news weekly or bi-weekly to keep site fresh
- Users expect updated content
- Search engines favor regularly updated sites
- Engagement increases with fresh news

**2. SEO Optimization**:
- **Titles**: 50-60 characters, include keywords
  - Bad: "Post"
  - Good: "ASOG TBI Announces New Accelerator Program"
- **Slugs**: Use hyphens, be descriptive
  - Bad: "post-123"
  - Good: "new-accelerator-program-2024"
- **Descriptions**: 150-160 characters, compelling
  - Appears in search results
  - Should encourage clicks
- **Images**: Add alt text for all images
  - Helps SEO and accessibility
  - Example: "Team celebrating program launch"
- **Keywords**: Research relevant search terms
  - Include naturally in content
  - Avoid keyword stuffing

**3. Image Optimization**:
- **File Size**: Compress before upload
  - Tools: TinyPNG, ImageOptim, or hosting provider tools
  - Target: <200KB for web images
  - Large files slow down website
- **Dimensions**: Follow recommended sizes
  - Featured images: 1200x600px or 1200x800px
  - Icons: 100x100px
  - Logos: 200x200px minimum
- **Format**: Use appropriate format
  - JPG: Photographs (best compression)
  - PNG: Graphics with transparency
  - SVG: Icons and logos (scalable)
  - WebP: Modern format (better compression)
- **Alt Text**: Always add descriptive alt text
  - Example: "ASOG TBI founder speaking at conference"
  - Helps accessibility and SEO

**4. Sort Order Management**:
- Use numbers to control display order
  - 1 = appears first
  - 2 = appears second
  - etc.
- Lower numbers always appear first
- Batch update sort order when adding new content
- Review periodically and reorganize as needed

**5. Content Review Before Publishing**:
- **Spell Check**: Use browser spell checker or tool
  - Read carefully to catch errors
  - Use Grammarly or similar tool
  - Ask colleague to proofread
- **Link Verification**: 
  - Test all links work
  - Links should open in correct context
  - External links should open in new tab
- **Format Check**:
  - Verify heading hierarchy (H1, H2, H3)
  - Check bullet points and lists
  - Ensure images display correctly
  - Test on mobile and desktop
- **Information Accuracy**:
  - Verify dates and numbers
  - Check company names and titles
  - Confirm URLs and contact info
  - Have subject matter expert review

**6. Testing Across Devices**:

**Desktop Testing**:
- Chrome, Firefox, Safari, Edge
- Different screen sizes (1920x1080, 1366x768)
- Test with zoom (100%, 110%, 125%)

**Mobile Testing**:
- iOS (iPhone) and Android phones
- Portrait and landscape orientation
- Touch interactions work smoothly
- Images display properly
- Text is readable without zooming
- Forms are easy to fill

**Tablet Testing**:
- iPad and Android tablets
- Both portrait and landscape
- Navigation accessible

**Test Methods**:
- Physical devices (most accurate)
- Browser DevTools responsive mode (good for quick testing)
- BrowserStack or similar cloud testing (various devices)
- Ask colleagues to test on their devices

**7. Backup Procedures**:

**Database Backups**:
- Schedule daily automated backups
- Download backup locally monthly
- Test restore process quarterly
- Keep backups for at least 30 days
- Document backup/restore process

**Media File Backups**:
- Copy `/public/uploads/` folder monthly
- Store in separate location or cloud drive
- Document which folders contain important files
- Keep version history

**Database Schema**:
- Keep copy of `asogtbi_schema.sql`
- Update after major changes
- Document schema changes in comments

---

### Content Calendar & Planning

**Recommended Schedule**:

**Daily**:
- Check for submissions/user inquiries
- Monitor website performance
- Respond to comments (if enabled)

**Weekly**:
- Review analytics
- Plan next week's content
- Publish 1-2 news items

**Monthly**:
- Review all programs/facilities for accuracy
- Update incubatee information
- Backup database
- Analyze traffic and engagement
- Plan promotional content

**Quarterly**:
- Comprehensive content audit
- Update outdated information
- Review and reorganize sort orders
- Test all forms and functionality
- Plan for upcoming events

---

### Troubleshooting & Common Issues

#### Google OAuth Login Not Working

**Symptoms**: 
- "Google Login" button doesn't work
- Redirects to error page
- "Invalid credentials" message

**Solutions**:
1. **Verify Credentials in `.env`**:
   - Check `googleOAuthClientId` is correct
   - Check `googleOAuthClientSecret` is correct
   - Both values copied exactly from Google Cloud Console
   - No extra spaces or characters

2. **Check Google Cloud Console**:
   - Logged in at https://console.cloud.google.com
   - OAuth 2.0 Client ID is created
   - Client is web application type
   - Authorized redirect URIs include:
     - `http://localhost:8080/asog-admin/google/callback` (local)
     - `https://asogtbi.com/asog-admin/google/callback` (production)

3. **Verify Admin Account**:
   - Your Google email exists in `admins` table
   - Check `googleEmail` field matches your Google account
   - Check `isActive = 1`
   - Database column exists (may need migration)

4. **Browser Issues**:
   - Clear browser cookies/cache
   - Try in Incognito/Private mode
   - Try different browser
   - Check browser allows popups/redirects

5. **Server Issues**:
   - Check server has internet connection
   - Verify domain SSL certificate is valid
   - Check server logs for errors
   - Restart web server

**Recovery**:
- Ask hosting support to verify OAuth redirect is working
- Manually test redirect URL in browser
- Temporarily enable traditional login as backup

---

#### Admin Can't See Published Content on Frontend

**Symptoms**:
- Content is published but not visible on website
- Shows "Draft" status even though marked published
- Admin can see in dashboard but users can't see

**Solutions**:
1. **Check Published Status**:
   - Go to content edit page
   - Verify **Status** is set to "Published" (not Draft or Scheduled)
   - Save changes if modified
   - Check that **"Published Date/Time"** is not in the future

2. **Verify Sort Order**:
   - Check **Sort Order** number is set
   - Lower numbers appear first
   - If all items have high sort order, none may display
   - Try setting sort order to 1

3. **Check Display Logic**:
   - Some pages only show first 10 items
   - Item may be in database but off current page
   - Verify item appears in table/database
   - Click pagination to see more items

4. **Clear Website Cache**:
   - If site has caching enabled, cache might be stale
   - Go to Admin → Settings → **Clear Cache**
   - Or wait for cache to expire (typically 1 hour)
   - Try hard refresh in browser (Ctrl+Shift+Delete)

5. **Database Verification**:
   - Access phpMyAdmin
   - Open the relevant table (posts, programs, facilities, incubatees)
   - Find your content by title
   - Verify `isPublished = 1`
   - Verify `publishedAt` date is not NULL and not in future
   - Correct in database if needed

**Example SQL Query**:
```sql
SELECT id, title, isPublished, publishedAt, sortOrder 
FROM posts 
WHERE title LIKE '%your-post-title%';
```
- Shows if content is published and when

---

#### File Upload Issues

**Symptoms**:
- Image doesn't upload
- "File too large" error
- "Invalid file type" error
- Upload starts but doesn't complete
- Image appears but doesn't display

**Solutions**:

1. **Check File Size**:
   - Maximum upload usually 50MB
   - For images, compress to <5MB
   - Tools: TinyPNG.com, ImageOptim
   - Verify actual file size before uploading

2. **Verify File Type**:
   - Allowed: JPG, PNG, SVG, WebP
   - Not allowed: EXE, ZIP, DOCX, etc.
   - Upload images only
   - Check file extension is correct

3. **Check Upload Directory Permissions**:
   - `/public/uploads/` must be writable
   - SSH or hosting panel → File Manager
   - Right-click `/public/uploads/` → Properties
   - Set permissions to 755 (or as instructed)
   - May need to ask hosting support

4. **Verify Storage Space**:
   - Check server disk space available
   - If disk full, delete old files or upgrade
   - Contact hosting provider if unsure

5. **Try Different File**:
   - Test with different image
   - If one file uploads fine, problem is specific file
   - Use different name or compress more

6. **Browser Issues**:
   - Try different browser
   - Clear browser cache
   - Try in Incognito mode
   - Check internet connection stability

7. **Network Issues**:
   - For large files, upload may timeout
   - Increase server timeout setting (hosting panel)
   - Upload during off-peak hours
   - Try splitting large files

**If Image Displays Gray/Broken**:
1. Upload was successful but image path is wrong
2. Go to edit page
3. Delete image
4. Upload again and save
5. Verify image displays

---

#### Website Loads Slowly

**Causes & Solutions**:

1. **Large Image Files**:
   - Compress all images before uploading
   - Use TinyPNG or similar
   - Target: <200KB per image

2. **Too Many Images**:
   - Limit to essential images
   - Remove unused image uploads
   - Delete old/archived images

3. **Database Growth**:
   - Old data accumulates over time
   - Archive old posts instead of deleting
   - Optimize database
   - Contact hosting for support

4. **Server Issues**:
   - Check if server is overloaded
   - View hosting control panel for CPU/memory usage
   - Upgrade server if necessary
   - Contact hosting support

5. **Content Delivery Network (CDN)**:
   - Enable CDN to serve images faster
   - Cloudflare, BunnyCDN are options
   - Speeds up global access

6. **Caching**:
   - Enable caching if available
   - Reduces server load
   - Admin → Settings → Enable Caching

---

#### Lost Password Recovery

**If Admin Forgets Password**:

**Option 1: Self-Service** (if "Forgot Password" link available):
1. Go to `/asog-admin` login page
2. Click **"Forgot Password?"** link
3. Enter email address
4. Click **"Send Reset Link"**
5. Check email for reset link
6. Click link in email
7. Enter new password (twice)
8. Click **"Reset Password"**
9. Login with new password

**Option 2: Request from Admin with User Management**:
1. Admin with user management access logs in
2. Goes to **Admins** section
3. Finds admin who needs reset
4. Clicks **Edit**
5. Enters temporary password
6. Clicks **Update**
7. Sends temporary password via email
8. User receives password, logs in
9. System prompts to change password
10. User sets permanent password

**Option 3: Database Reset** (via SQL):
```sql
UPDATE admins 
SET password = SHA2('temporary123456', 256) 
WHERE email = 'admin@example.com';
```
- Admin logs in with "temporary123456"
- System prompts to change on first login
- May require direct database access

---

#### Getting Help & Support

**Resources**:
1. **Site Documentation**: Check this README.md
2. **CodeIgniter Docs**: https://codeigniter.com/user_guide/
3. **Hosting Support**: Contact Hostinger or your hosting provider
4. **Development Team**: Contact original developers
5. **Browser DevTools**: Press F12 to check for error messages

**When Reporting Issues**:
- Describe what you tried to do
- What error message appeared (if any)
- What you expected to happen
- What actually happened
- Provide screenshots if helpful
- Include any error messages from browser console (F12)

---

---

## Technical Requirements

### System Requirements

**Server Environment**:
- **OS**: Linux (Ubuntu 20.04+ recommended), Windows Server, or macOS
- **Web Server**: Apache 2.4+ with `mod_rewrite` or Nginx 1.18+
- **PHP**: Version 7.4 - 8.3
  - Required Extensions:
    - `gd` (image processing)
    - `curl` (HTTP requests)
    - `fileinfo` (file type detection)
    - `json` (JSON support)
    - `mbstring` (multibyte string)
    - `xml` (XML parsing)
    - `mysqli` or `pdo_mysql` (database)
    - `hash` (password hashing)
    - `filter` (filtering functions)
  - Recommended: OpenSSL extension for HTTPS

**Database**:
- **MySQL**: 5.7+ or MariaDB 10.2+
- **Minimum Storage**: 1GB for initial setup
- **Character Set**: UTF-8 (`utf8mb4`)
- **Collation**: `utf8mb4_general_ci` or `utf8mb4_unicode_ci`

**Browser Support** (for admin and users):
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari 14+, Chrome Android 90+)

**Build Tools** (for development only):
- **Node.js**: 14.0+
- **npm**: 6.0+
- **Composer**: Latest version

---

### Performance Specifications

**Page Load Times**:
- Homepage: Target < 3 seconds (first load), < 1 second (cached)
- Program pages: < 2 seconds
- Admin pages: < 2 seconds

**Storage**:
- Database: Starts at ~50MB, grows with content
- Media/uploads: Allocate 5-10GB for typical use
- Logs: ~100MB per month (configurable)

**Bandwidth**:
- Low traffic: 1GB/month sufficient
- Medium traffic: 5-10GB/month
- High traffic: 20GB+/month

---

## Development

### Initial Setup

#### Prerequisites Checklist

Before starting, ensure you have:
- ✓ Access to source code (GitHub, ZIP file, etc.)
- ✓ Local development environment or hosting access
- ✓ Database credentials (username, password, database name)
- ✓ Google OAuth credentials (client ID and secret)
- ✓ Text editor or IDE (VS Code, PHPStorm, etc.)
- ✓ Terminal/command line access
- ✓ Composer installed globally
- ✓ Node.js and npm installed

#### Step-by-Step Installation

**Step 1: Clone or Download Source Code**

**Option A: Using Git** (if code is on GitHub):
```bash
git clone https://github.com/your-org/asog-website.git
cd asog-website
```

**Option B: Download ZIP**:
1. Download ZIP file from GitHub or file share
2. Extract to your desired location
3. Open terminal/command prompt in extracted folder

**Step 2: Install PHP Dependencies**

```bash
composer install
```

**What This Does**:
- Downloads all required PHP libraries
- Creates `vendor/` folder with all dependencies
- Generates `autoload.php` for code loading
- May take 1-2 minutes depending on internet speed

**Troubleshooting**:
- If error: "composer: command not found"
  - Download Composer from https://getcomposer.org/
  - Install Composer globally
  - Then run `composer install` again

**Step 3: Configure Environment**

Create `.env` file from template:

**Windows PowerShell**:
```powershell
Copy-Item env \.env
```

**macOS/Linux**:
```bash
cp env .env
```

**Or manually**:
1. Copy `env` file
2. Rename copy to `.env`
3. Edit in text editor

**Step 4: Edit `.env` Configuration**

Open `.env` in text editor and update these critical settings:

```ini
# Application
CI_ENVIRONMENT = development      # or 'production'
app.baseURL = 'http://localhost:8080/'  # or your domain

# Database
database.default.hostname = localhost
database.default.database = asogtbi
database.default.username = root        # Your database user
database.default.password = 'password'  # Your database password
database.default.port = 3306           # MySQL default port

# Google OAuth
googleOAuthClientId = 'YOUR_CLIENT_ID.apps.googleusercontent.com'
googleOAuthClientSecret = 'YOUR_CLIENT_SECRET'
googleOAuthAllowedDomains = 'gmail.com,example.edu.ph'  # Optional: domain restrictions
```

**How to Get Google OAuth Credentials**:

1. Go to https://console.cloud.google.com
2. Log in with your Google account
3. Create new project: Click "Select a Project" → "New Project"
4. Enter project name: "ASOG-TBI"
5. Click "Create"
6. Wait for project to create
7. Go to "APIs & Services" → "Credentials"
8. Click "Create Credentials" → "OAuth 2.0 Client ID"
9. Choose "Web application"
10. Add authorized redirect URIs:
    - `http://localhost:8080/asog-admin/google/callback` (local testing)
    - `https://asogtbi.com/asog-admin/google/callback` (production)
11. Copy **Client ID** and **Client Secret**
12. Paste into `.env` file

**Step 5: Setup Database**

**Option A: Import SQL File** (recommended for first setup):

```bash
mysql -u root -p asogtbi < asogtbi_schema.sql
```
- Enter your MySQL password when prompted
- Database tables are created and populated
- Ready to use immediately

**Option B: Using phpMyAdmin** (if available):
1. Open phpMyAdmin from hosting control panel
2. Create database named `asogtbi`
3. Go to **Import** tab
4. Select `asogtbi_schema.sql` file
5. Click **Import**
6. Verify tables appear

**Option C: Using MySQL Command Line**:
```bash
mysql -u root -p
```
Then:
```sql
CREATE DATABASE asogtbi CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE asogtbi;
SOURCE asogtbi_schema.sql;
EXIT;
```

**Verify Database Setup**:
```sql
SHOW DATABASES;  -- Should show 'asogtbi'
USE asogtbi;
SHOW TABLES;     -- Should show 10+ tables
```

**Step 6: Create Initial Admin User**

```sql
INSERT INTO admins (fullName, email, password, role, isActive, googleEmail, createdAt, updatedAt)
VALUES (
  'Admin Name',
  'admin@yourdomain.com',
  SHA2('tempPassword123', 256),
  'admin',
  1,
  'admin@gmail.com',  -- Your Google account email
  NOW(),
  NOW()
);
```

Replace values:
- `'Admin Name'` → Your name
- `'admin@yourdomain.com'` → Your email
- `'tempPassword123'` → Temporary password
- `'admin@gmail.com'` → Your Google account email

**Step 7: Create Writable Directories**

```bash
# Create directories for uploads and cache
mkdir -p public/uploads writable/uploads writable/cache writable/logs writable/session
chmod 755 public/uploads writable
```

**Windows Users**:
- No need to set permissions manually
- Just verify folders exist

**Step 8: Install Frontend Dependencies**

```bash
npm install
```

**What This Does**:
- Downloads JavaScript and CSS tools
- Creates `node_modules/` folder
- Takes 1-3 minutes

**Step 9: Build CSS with Tailwind**

```bash
npm run tailwind
```

**What This Does**:
- Compiles Tailwind CSS
- Creates optimized `public/style.css`
- Scans HTML files for used classes
- Produces minimal CSS file

**Watch Mode** (for development - auto-rebuilds):
```bash
npm run tailwind -- --watch
```
- Keeps running in terminal
- Rebuilds CSS whenever files change
- Press Ctrl+C to stop

---

### Running the Website Locally

#### Start Development Server

```bash
php spark serve
```

**Output Shows**:
```
CodeIgniter 4.x
Server running at http://127.0.0.1:8080
Press Control+C to stop
```

**Access in Browser**:
- **Frontend**: http://localhost:8080
- **Admin Dashboard**: http://localhost:8080/asog-admin
- **Google Login**: http://localhost:8080/asog-admin (click "Sign in with Google")

**Verify it's Working**:
1. Open http://localhost:8080 in browser
2. Homepage loads with all content
3. Navigate through pages (Programs, News, etc.)
4. Visit admin at http://localhost:8080/asog-admin
5. Should redirect to Google login

**Troubleshooting**:
- If "Connection refused": Server not running, run `php spark serve` again
- If "Page not found": URL may be wrong, check path
- If CSS doesn't load: Run `npm run tailwind` to compile
- If Google login fails: Check `.env` has correct credentials

#### Stop Development Server

Press **Ctrl+C** in terminal where server is running

---

### Running Tests

#### Setup Test Database

**Create separate test database** (doesn't overwrite development data):

```bash
mysql -u root -p
```

Then:
```sql
CREATE DATABASE asogtbi_test CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

#### Run All Tests

```bash
php vendor/bin/phpunit
```

**Output Shows**:
- Number of tests run
- Passed (green) or failed (red)
- Performance timing
- Code coverage (if configured)

**Example Output**:
```
PHPUnit 9.x
OK (45 tests, 150 assertions)
Time: 2.34s, Memory: 10.50 MB
```

#### Run Specific Test File

```bash
php vendor/bin/phpunit tests/unit/Controllers/HomeTest.php
```

#### Run with Coverage Report

```bash
php vendor/bin/phpunit --coverage-html coverage/
```

Creates HTML report in `coverage/` folder showing which code is tested

#### Test Database Configuration

In `.env.test` (if exists) or `.env`:

```ini
database.default.hostname = localhost
database.default.database = asogtbi_test  # Use test database
database.default.username = root
database.default.password = 'password'
```

---

### Building for Production

#### Step 1: Optimize Autoloader

```bash
composer install --optimize-autoloader --no-dev
```

- Removes development packages
- Optimizes class loading
- Faster autoloader for production

#### Step 2: Build CSS (production mode)

```bash
npm run tailwind:build
```

Or regular build:
```bash
npm run tailwind
```

- Creates optimized, minified CSS
- Removes unused styles
- Smaller file size

#### Step 3: Minify JavaScript** (if applicable)

```bash
npm run js:build
```

Or if not configured, manually minify JS files

#### Step 4: Update `.env` for Production

```ini
CI_ENVIRONMENT = production

# Security - Change these
app.baseURL = 'https://asogtbi.com/'
app.encryptionKey = 'YOUR_RANDOM_ENCRYPTION_KEY'

# Database - Use production database
database.default.hostname = prod-mysql-server
database.default.database = asogtbi_prod
database.default.username = prod_user
database.default.password = 'STRONG_PASSWORD'

# Disable debugging
CI_DEBUG = false
log.threshold = 1

# HTTPS enforcement
app.forceSecure = true
```

**Generate Encryption Key**:
```bash
php spark key:generate
```

This creates a random secure key for encryption

#### Step 5: Clear Cache

```bash
php spark cache:clear
php spark config:cache
php spark routes:cache
```

- Removes old cached data
- Rebuilds optimized caches
- Improves performance

#### Step 6: Deploy to Server

**Using Hosting Control Panel**:
1. Log into hosting provider (Hostinger, etc.)
2. Access **File Manager**
3. Upload all files (except `.git`, `node_modules`, `vendor/` - upload via Composer)
4. Set correct permissions (755 for folders, 644 for files)
5. Upload `.env` file (keep outside public_html if possible)

**Using Git** (if provider supports):
```bash
cd /path/to/hosting
git clone https://github.com/org/repo.git .
composer install --optimize-autoloader --no-dev
npm install && npm run tailwind:build
# Configure .env on server
```

**Using FTP/SCP**:
1. Connect to server via FTP client
2. Upload all project files
3. Set proper permissions
4. Configure `.env` directly on server

#### Step 7: Verify Production Setup

1. Visit https://asogtbi.com in browser
2. Verify homepage loads
3. Test admin login: https://asogtbi.com/asog-admin
4. Check error logs: `writable/logs/`
5. Monitor server resources: Hosting control panel

**Check Server Logs for Errors**:
```bash
tail -f writable/logs/log-2024-05-02.log
```
- Shows real-time errors
- Helps identify issues
- Ctrl+C to stop

---

### Database Migrations (if applicable)

#### Create New Migration

When database schema needs to change:

```bash
php spark make:migration add_column_to_posts
```

**This Creates** a file like `Database/Migrations/2024-05-02-120000_AddColumnToPosts.php`

#### Run Migrations

```bash
php spark migrate
```

- Applies all pending migrations
- Updates database schema
- Updates `db_migrations` table

#### Rollback Migration

```bash
php spark migrate:rollback
```

- Reverts last migration
- Useful if migration fails
- Can rollback multiple times

#### Reset Database

```bash
php spark migrate:refresh
```

- Removes all tables
- Runs all migrations again
- Used for testing/reset
- **⚠️ WARNING: Deletes all data!**

---

### Common Development Tasks

#### Clearing Cache

```bash
php spark cache:clear
```

- Clears application cache
- Use after making changes
- Helps with debugging

#### View Routes

```bash
php spark routes
```

Lists all application routes and methods:
```
+---------+------------------+------------------+
| Method  | Route            | Handler          |
+---------+------------------+------------------+
| GET     | /                | Home::index      |
| GET     | /programs        | Programs::index  |
| POST    | /contact/submit  | Contact::submit  |
+---------+------------------+------------------+
```

#### Debug Mode

Enable in `.env`:
```ini
CI_DEBUG = true
```

**Shows**:
- Detailed error messages
- Code stack traces
- Query debugging
- ⚠️ Only use in development, disable in production

#### Database Seeding (optional)

Add test data:

```bash
php spark db:seed SampleSeeder
```

Creates sample posts, programs, etc. for testing

---

### Code Structure Overview

```
asog-website/
├── app/                      # Application code
│   ├── Config/              # Configuration files
│   ├── Controllers/         # Page controllers (Home, About, etc.)
│   ├── Models/              # Database models
│   ├── Views/               # HTML templates
│   │   ├── templates/       # Layout templates
│   │   ├── pages/           # Page content
│   │   └── admin/           # Admin interface
│   ├── Database/            # Migrations and seeds
│   └── Filters/             # Request filters (Auth, etc.)
│
├── public/                  # Web-accessible files
│   ├── index.php           # Entry point
│   ├── assets/             # CSS, JavaScript, images
│   ├── uploads/            # User-uploaded media
│   └── robots.txt          # SEO robots file
│
├── writable/               # Server writable directories
│   ├── cache/              # Temporary cache
│   ├── logs/               # Application logs
│   ├── session/            # Session files
│   └── uploads/            # Backup upload location
│
├── vendor/                 # Composer packages (auto-generated)
├── node_modules/           # npm packages (auto-generated)
├── composer.json           # PHP dependencies
├── package.json            # JavaScript dependencies
├── .env                    # Environment configuration
└── spark                   # CodeIgniter CLI tool
```

**Key Directories**:
- **app/Views/admin/**: Admin dashboard HTML templates
- **app/Controllers/Admin/**: Admin logic and processing
- **public/assets/**: CSS, JavaScript, images
- **writable/**: Logs, cache, uploads (must be writable)

---

### Deployment Checklist

Before going live to production:

**Security**:
- ✓ `.env` file contains strong passwords
- ✓ `CI_ENVIRONMENT = production` in `.env`
- ✓ Debug mode disabled
- ✓ SSL certificate installed (HTTPS)
- ✓ Database backups configured
- ✓ File permissions set correctly (755 folders, 644 files)

**Performance**:
- ✓ CSS and JS minified
- ✓ Images optimized and compressed
- ✓ Caching enabled
- ✓ Database optimized
- ✓ CDN configured (if applicable)

**Functionality**:
- ✓ All pages tested and working
- ✓ Forms submit correctly
- ✓ Admin login works
- ✓ Images display properly
- ✓ Links navigate correctly
- ✓ Mobile responsive

**Monitoring**:
- ✓ Error logging enabled
- ✓ Uptime monitoring set up
- ✓ Performance monitoring enabled
- ✓ Regular backups scheduled
- ✓ Email alerts configured

**Documentation**:
- ✓ Database schema documented
- ✓ Admin credentials stored securely
- ✓ Backup procedures documented
- ✓ Contact info for support updated

---

## Maintenance & Operations

### Regular Maintenance Tasks

**Daily**:
- Monitor website uptime
- Check error logs
- Respond to user inquiries
- Back up database (if not automated)

**Weekly**:
- Review website analytics
- Check for security updates
- Publish new content
- Test admin functions

**Monthly**:
- Full database backup
- Update software/packages
- Security audit
- Performance review
- Cleanup old logs and cache

**Quarterly**:
- Update PHP, MySQL, server software
- Security penetration test
- Backup restoration test
- Content audit and refresh

### Monitoring & Logging

**Access Logs** (who visited):
```bash
tail -f /var/log/apache2/access.log  # Apache
tail -f /var/log/nginx/access.log    # Nginx
```

**Error Logs** (what went wrong):
```bash
cat writable/logs/log-2024-05-02.log
```

**Database Logs** (SQL queries):
- Enable in `.env`:
```ini
database.default.logQueries = true
```

### Performance Optimization

**Database**:
- Add indexes to frequently searched columns
- Archive old posts (move to `archived_posts` table)
- Optimize tables regularly: `OPTIMIZE TABLE posts;`

**Caching**:
- Enable database query caching
- Use PHP Opcache
- Cache HTTP headers
- Implement CDN for static files

**Images**:
- Compress all images before upload
- Use WebP format when possible
- Implement lazy loading
- Resize large images

---

## Getting Help & Support

### Documentation & Resources

**Official Resources**:
- **CodeIgniter Guide**: https://codeigniter.com/user_guide/
- **CodeIgniter Forums**: https://forum.codeigniter.com/
- **GitHub Issues**: Report bugs on project repository

**Community Help**:
- Stack Overflow: Tag questions with `codeigniter` and `codeigniter-4`
- Reddit: r/codeigniter
- Discord Communities: CodeIgniter official Discord

### Reporting Issues

When reporting bugs or requesting help:

1. **Describe the Issue**:
   - What were you trying to do?
   - What happened?
   - What did you expect to happen?

2. **Provide Details**:
   - Steps to reproduce
   - Error messages (exact text)
   - Screenshots if helpful
   - Browser/OS information

3. **Include Logs**:
   - Error log contents
   - Browser console errors (F12)
   - Database errors

4. **Environment Info**:
   - PHP version
   - MySQL version
   - Hosting provider
   - Local or production?

### Example Issue Report

```
Title: Admin Can't Upload Images

Description:
I'm trying to upload a program icon image but getting an error.

Steps to Reproduce:
1. Log into admin dashboard
2. Go to Programs section
3. Click "Create New Program"
4. Try to upload an SVG image
5. Get error message

Error Message:
"File upload failed: Invalid file type"

Details:
- File: program-icon.svg (2 MB)
- PHP Version: 7.4
- Server: Hostinger
- Admin role: full admin

Expected:
Image should upload and appear in form
```

This gives developers all information needed to help quickly.

---

## Glossary of Terms

**Admin/Administrator**: User account with permission to manage website content and settings

**Cohort**: A group/batch of incubatees in the program (e.g., Cohort 2024)

**CMS**: Content Management System - the admin interface for managing site content

**CodeIgniter**: PHP web framework used to build this website

**Database**: Storage system for all website data (MySQL)

**Draft**: Content created but not yet published (not visible to public)

**Frontend**: The public-facing website that visitors see

**GD Library**: PHP graphics library for image processing

**Incubatee**: A startup or company in the ASOG TBI program

**Laravel Homestead**: Local development environment (alternative setup)

**Migration**: Database schema change/update script

**OAuth**: Google authentication method for admin login

**Published**: Content made visible to public website visitors

**Repository**: Where code is stored (GitHub, GitLab, etc.)

**Responsive Design**: Website that adapts to different screen sizes

**REST API**: Interface for applications to communicate with website

**SEO**: Search Engine Optimization - making site visible in search results

**Seeding**: Populating database with test/sample data

**Slug**: URL-friendly identifier (e.g., "my-program" becomes /programs/my-program)

**Template**: Reusable HTML structure (layout.php, etc.)

**WYSIWYG Editor**: What-You-See-Is-What-You-Get editor for content creation

---

## Version History

**Current Version**: 1.0
**Last Updated**: May 2, 2024
**CodeIgniter**: 4.x
**PHP**: 7.4 - 8.3
**Database**: MySQL 5.7+ / MariaDB 10.2+

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support & Contact

For support, feature requests, or issues:
- **Email**: admin@asogtbi.com (update with actual contact)
- **GitHub Issues**: [Project Repository](https://github.com/your-org/asog-website/issues)
- **Documentation**: See this README.md file
- **Status Page**: https://status.asogtbi.com (if available)

**Deactivate an admin account:**
```sql
UPDATE admins 
SET isActive = 0 
WHERE email = 'admin@yourdomain.com';
```

### Finding Your Google Account Sub ID

To find your Google account's stable **Sub ID**:
1. Sign in to your app using Google OAuth.
2. Check the application logs or error output for the OAuth token details.
3. The `sub` claim in the token is your Google Sub ID.
4. Alternatively, you can leave it blank and just use `googleEmail` for simpler setups.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
=======
# asog-website
A website for the CSPC ASOG TBI
