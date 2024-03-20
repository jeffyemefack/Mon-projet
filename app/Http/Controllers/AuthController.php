<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Méthode pour l'inscription d'un utilisateur
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'lastname'=>$request->lastname,
            'phone'=>$request->phone,
            'address'=>$request->address,
            'poste'=>$request->poste,
            'role'=>$request->role,
            'password' => bcrypt($request->password),
        ]);

        $user->save();
      if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension();
    
        $image->move(public_path('images'), $imageName);
        $imagess = 'images/' . $imageName;
        
        $imageModel = new Image([
            'user_id' => $user->id,
            'image' => url($imagess)
        ]);
        
        $imageModel->save();
      }

        return response()->json(['message' => 'Utilisateur inscrit avec succès'], 201);
    }

    // Méthode pour la connexion d'un utilisateur
    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Échec de la création du token'], 500);
        }
        return response()->json(['token' => $token]);
        
    }

    // Méthode pour la déconnexion d'un utilisateur
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Utilisateur déconnecté']);
    }

    // Méthode pour rafraîchir le token JWT
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();
        } catch (JWTException $e) {
            return response()->json(['message' => 'Échec de la création du token'], 500);
        }

        return response()->json(['token' => $token]);
    }




    // Méthode pour obtenir les informations de l'utilisateur connecté
    public function me()
    {
        $user = Auth::user();

        // return response()->json($user);
        // $user = Auth::user();
        $img = Image::where('user_id', $user->id)->first();
        if ($img) {
            $imageName = $img->image;
        return response()->json([$user, $img ]);
        }else {
        return response()->json([$user]);
        }
    }



    


    public function delete(Request $request, $id)
{
    try {
        // Trouver l'élément à supprimer dans la base de données
        $element = User::find($id);

        // Supprimer l'élément
        $element->delete();

        // Retourner une réponse JSON indiquant que la suppression a réussi
        return response()->json(['message' => 'Suppression réussie']);
    } catch (\Exception $e) {
        // En cas d'erreur, retourner une réponse JSON avec le message d'erreur
        return response()->json(['message' => 'Erreur lors de la suppression'], 500);
    }
}
}