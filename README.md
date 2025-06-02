# Laravel Blog API with JWT Authentication

This project provides a RESTful API for user authentication and blog management, secured using JWT tokens. It supports CRUD operations on blogs, user profile management, and media handling with Markdown-based blog bodies.

---

## API Routes

### Public Routes

- `POST api/register` — Register a new user  
- `POST api/login` — Login and receive JWT token  
- `GET api/blogs` — List all blogs  
- `GET api/blogs/{blog}` — View a single blog by ID  
- `GET api/blog-image` — List all blog media (images)

### Protected Routes (Require JWT Authentication)

These routes require including a valid JWT token in the `Authorization` header.

- `GET api/profile` — Get logged-in user profile  
- `PUT api/edit-profile` — Edit logged-in user profile  
- `POST api/change-password` — Change logged-in user password  
- `GET api/logout` — Logout the user  

- `POST api/blogs` — Create a new blog  
- `PUT api/blogs/{blog}` — Update an existing blog  
- `DELETE api/blogs/{blog}` — Delete a blog  

- `POST api/blog-image` — Store an image for the blog  
- `DELETE api/blog-image` — Delete a blog image
  
### Blog Media and Markdown Handling

When creating or editing a blog post, the API expects the blog body to be written in **Markdown** format. Images are handled through a separate upload process and embedded using Markdown syntax.

#### Image Handling Workflow

1. **Upload images separately**  
   Use the `POST api/blog-image` endpoint to upload images before submitting the blog post. This will store the image and return a `media_path_url` which is the storage link for the image.

2. **Embed media in Markdown**  
   Use the standard Markdown image syntax to include uploaded images in the blog body:
   
3. **Submit the blog post**  
Send the blog content with embedded image URLs using the `POST api/blogs` endpoint.

4. **Server-side processing**  
- The server extracts all image URLs from the Markdown body using regex.
- It flags the referenced images as `is_used = true` and associates them with the blog.
- Any uploaded images not included in the blog content are deleted to avoid unused media in storage.




