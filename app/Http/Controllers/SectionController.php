<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function index()
    {   
        // Define the custom order for Year Levels
        $yearLevels = ['1', '2', '3', '4'];

        // Retrieve sections and sort them first by Year Level, then by Section description (alphabetically)
        $sections = Section::whereIn('name', $yearLevels)
                           ->where('user_id', Auth::id())
                           ->orderByRaw("FIELD(name, '1', '2', '3', '4')")
                           ->orderBy('description', 'asc')
                           ->get();
    
        return view('sections.index', compact('sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
    
        $sectionExists = Section::where('name', $request->name)
                                ->where('description', $request->description)
                                ->where('user_id', Auth::id())
                                ->exists();
        
        if ($sectionExists) {
            return redirect()->back()->with('error', 'Section already exists for this year level.');
        }
        
        Section::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);
        
        return redirect()->route('sections.index')->with('success', 'Section created successfully.');
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'edit_name' => 'required',
            'edit_description' => 'required',
        ]);
    
        $sectionExists = Section::where('name', $request->edit_name)
                                ->where('description', $request->edit_description)
                                ->where('user_id', Auth::id())
                                ->exists();
        
        if ($sectionExists) {
            return redirect()->back()->with('error', 'Section already exists for this year level.');
        }
    
        $section->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
        ]);
        
        return redirect()->route('sections.index')->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        if ($section->user_id != Auth::id()) {
            return redirect()->route('sections.index')->with('error', 'You are not authorized to delete this section.');
        }

        $section->delete();

        return redirect()->route('sections.index')->with('success', 'Section deleted successfully.');
    }

    // Show students in a section
    public function showStudents($id)
    {
        $section = Section::findOrFail($id);
        $students = Student::where('section_id', $id)->get();

        return view('sections.students', compact('section', 'students'));
    }

    // API
    public function getSectionApi()
    {   
        $sections = Section::where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'sections' => $sections,
        ]);
    }

    public function getSectionDetailsApi($id)
    {
        $section = Section::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$section) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found or you are not authorized to view it.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'section' => $section,
        ]);
    }

    public function storeSectionApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section = new Section();
        $section->name = $request->name;
        $section->description = $request->description;
        $section->user_id = Auth::id();
        $section->save();

        return response()->json(['success' => true, 'section' => $section], 201);
    }

    public function updateSectionDetailsApi(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section = Section::find($id);

        if (!$section) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found',
            ], 404);
        }

        $section->name = $validatedData['name'];
        $section->description = $validatedData['description'] ?? $section->description;
        $section->save();

        return response()->json([
            'success' => true,
            'section' => $section,
            'message' => 'Section updated successfully',
        ]);
    }

    public function destroySectionApi(Section $section)
    {
        if ($section->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted successfully.'], 200);
    }

    public function getSectionsByYear($year)
    {
        // Adjust 'year_level' field according to your database structure
        $sections = Section::where('year_level', $year)->get(['id', 'name']);
        
        // Return JSON response
        return response()->json(['sections' => $sections]);
    }

}
